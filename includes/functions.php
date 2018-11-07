<?php
 
function sec_session_start() {
    $session_name = 'sec_session_id';   // vergib einen Sessionnamen
    $secure = true;
    // Damit wird verhindert, dass JavaScript auf die session id zugreifen kann.
    $httponly = true;
    // Zwingt die Sessions nur Cookies zu benutzen.
    if (ini_set('session.use_only_cookies', 1) === FALSE) {
        header("Location: ../error.php?err=Konnte keine sichere Sitzung initialisieren (ini_set)");
        exit();
    }
    // Holt Cookie-Parameter.
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params($cookieParams["lifetime"],
        $cookieParams["path"], 
        $cookieParams["domain"], 
        $secure,
        $httponly);
    // Setzt den Session-Name zu oben angegebenem.
    session_name($session_name);
    session_start();            // Startet die PHP-Sitzung 
    session_regenerate_id();    // Erneuert die Session, löscht die alte. 
}

function login($email, $password, $mysqli) {
    // Das Benutzen vorbereiteter Statements verhindert SQL-Injektion.
    if ($stmt = $mysqli->prepare("SELECT id, password, salt
        FROM users
       WHERE email = ?
	LIMIT 1")) {
        $stmt->bind_param('s', $email);  // Bind "$email" to parameter.
        $stmt->execute();    // Führe die vorbereitete Anfrage aus.
        $stmt->store_result();

        // hole Variablen von result.
        $stmt->bind_result($user_id, $db_password, $salt);
        $stmt->fetch();

        // hash das Passwort mit dem eindeutigen salt.
        $password = hash('sha512', $password . $salt);
        if ($stmt->num_rows == 1) {
            // Wenn es den Benutzer gibt, dann wird überprüft ob das Konto
            // blockiert ist durch zu viele Login-Versuche

            if (checkbrute($user_id, $mysqli) == true) {
                // Konto ist blockiert
                return 1;
            } else {
                // Überprüfe, ob das Passwort in der Datenbank mit dem vom
                // Benutzer angegebenen übereinstimmt.
                if ($db_password == $password) {
                    // Passwort ist korrekt!
                    // Hole den user-agent string des Benutzers.
                    $user_browser = $_SERVER['HTTP_USER_AGENT'];
                    // XSS-Schutz, denn eventuell wir der Wert gedruckt
                    $user_id = preg_replace("/[^0-9]+/", "", $user_id);
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['login_string'] = hash('sha512',
                              $password . $user_browser);
                    // Login erfolgreich.
                    return 0;
                } else {
                    // Passwort ist nicht korrekt
                    // Der Versuch wird in der Datenbank gespeichert
                    $now = time();
                    $mysqli->query("INSERT INTO login_attempts(user_id, time)
                                    VALUES ('$user_id', '$now')");
                    return 2;
                }
            }
        } else {
            //Es gibt keinen Benutzer.
            return 3;
        }
    }
}

function checkbrute($user_id, $mysqli) {
    // Hole den aktuellen Zeitstempel
    $now = time();

    // Alle Login-Versuche der letzten zwei Stunden werden gezählt.
    $valid_attempts = $now - (2 * 60 * 60);

    if ($stmt = $mysqli->prepare("SELECT time
                             FROM login_attempts
                             WHERE user_id = ?
                            AND time > '$valid_attempts'")) {
        $stmt->bind_param('i', $user_id);

        // Führe die vorbereitet Abfrage aus.
        $stmt->execute();
        $stmt->store_result();

        // Wenn es mehr als 5 fehlgeschlagene Versuche gab
        if ($stmt->num_rows > 5) {
            return true;
        } else {
            return false;
        }
    }
}

function login_check($mysqli) {
    // Überprüfe, ob alle Session-Variablen gesetzt sind
    if (isset($_SESSION['user_id'],
                        $_SESSION['login_string'])) {

        $user_id = $_SESSION['user_id'];
        $login_string = $_SESSION['login_string'];

        // Hole den user-agent string des Benutzers.
        $user_browser = $_SERVER['HTTP_USER_AGENT'];

        if ($stmt = $mysqli->prepare("SELECT password
                                      FROM users
                                      WHERE id = ? LIMIT 1")) {
            // Bind "$user_id" zum Parameter.
            $stmt->bind_param('i', $user_id);
            $stmt->execute();   // Führe das prepared-Statement aus
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                // Wenn es den Benutzer gibt, hole die Variablen von result.
                $stmt->bind_result($password);
                $stmt->fetch();
                $login_check = hash('sha512', $password . $user_browser);

                if ($login_check == $login_string) {
                    // Eingeloggt!
                    return $user_id;
                } else {
                    // Nicht eingeloggt
                    return 0;
                }
            } else {
                // Nicht eingeloggt
                return 0;
            }
        } else {
            // Nicht eingeloggt
            return 0;
        }
    } else {
        // Nicht eingeloggt
        return 0;
    }
}

function admin_check($mysqli) {	//überprüfe ob angemeldeter Benutzer höhere Rechte hat
	$user_id = $_SESSION['user_id'];
	if ($stmt = $mysqli->prepare("SELECT level FROM users WHERE id = ? LIMIT 1")) {
            $stmt->bind_param('i', $user_id);
            $stmt->execute();   // Führe das prepared-Statement aus
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                // Wenn es den Benutzer gibt, hole die Variablen von result.
                $stmt->bind_result($level);
                $stmt->fetch();
                return $level;
            }
        }
}

function email_check($mysqli) {	//überprüfe Email Adresse von angemeldetem Benutzer
	if (isset($_SESSION['user_id'])) {
		$user_id = $_SESSION['user_id'];
		if ($stmt = $mysqli->prepare("SELECT email FROM users WHERE id = ? LIMIT 1")) {
			$stmt->bind_param('i', $user_id);
			$stmt->execute();   // Führe das prepared-Statement aus
			$stmt->store_result();

			if ($stmt->num_rows == 1) {
				// Wenn es den Benutzer gibt, hole die Variablen von result.
				$stmt->bind_result($email);
				$stmt->fetch();
				return $email;
			}
		}
	}
}

function req_auth($mysqli) {
	// Hole ID/Key Paar der HTTP Digest Authentifizierung
	$id = 1;	// falls irgendwann mehrere ID/Key Paare existieren (z.B. bei mehreren Druckern)
	if ($stmt = $mysqli->prepare("SELECT auth_id, auth_key
                                      FROM ultimaker_authentification
                                      WHERE id = ? LIMIT 1")) {
        $stmt->bind_param('i', $id);
		$stmt->execute();   // Führe das prepared-Statement aus
        $stmt->store_result();
		
		// hole Variablen von result.
        $stmt->bind_result($auth_id, $auth_key);
        $stmt->fetch();
		
		return array ( $auth_id, $auth_key );
	} else {
		return false;
	}
}

function esc_url($url) {

    if ('' == $url) {
        return $url;
    }

    $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);

    $strip = array('%0d', '%0a', '%0D', '%0A');
    $url = (string) $url;

    $count = 1;
    while ($count) {
        $url = str_replace($strip, '', $url, $count);
    }

    $url = str_replace(';//', '://', $url);

    $url = htmlentities($url);

    $url = str_replace('&amp;', '&#038;', $url);
    $url = str_replace("'", '&#039;', $url);

    if ($url[0] !== '/') {
        // Wir wollen nur relative Links von $_SERVER['PHP_SELF']
        return '';
    } else {
        return $url;
    }
}

function seconds_to_time($secs) {		// Für di Ausgabe der Druckdauer (die sich aus Sekunden errechnet)
    $seconds = $secs % 60;
	$minutes = $secs / 60 % 60;
	$hours = floor($secs / 3600);
	return $hours . ":" . floor($minutes) . ":" . floor($seconds);
}

<?php
/**
* Diese Datei enthält hauptsächlich die Funktionen zur Überprüfung ob ein Benutzer berechtigt ist etwas zu tun 
* und stammt zum allergrößten Teil von der in der @author-Annotation erwähnten Website
*
* @author   Tom Lehmann & https://de.wikihow.com/Ein-sicheres-Login-Skript-mit-PHP-und-MySQL-erstellen
* @version  1.0
* 
*/

/**
* Erstellt eine sichere Sitzung
* 
* @author https://de.wikihow.com/Ein-sicheres-Login-Skript-mit-PHP-und-MySQL-erstellen
*
* @modified	no
*/
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

/**
* Loggt einen Benuter über seine E-Mail/Passwort Kombination ein
* 
* @author https://de.wikihow.com/Ein-sicheres-Login-Skript-mit-PHP-und-MySQL-erstellen
*
* @modified	yes
*
* @param string	 $email		E-Mail Adresse des einzuloggenden Benutzers
* @param string  $password	Das Passwort des einzuloggenden Benutzers
* @param mysqli  $mysqli	Die zur Überprufung zu verwendende MYSQLi Verbindung
*
* @return int               0 steht für einen erfolgreichen Login
*							1 heißt das Konto ist blockiert
*							2 heißt das Passwort ist nicht korrekt
*							3 heißt es gibt den Benutzer nicht
*/
function login($email, $password, $mysqli) {
	$hfu_pattern = "/@hs-furtwangen\.de/";				// Preg-Match Pattern für die HFU-Mail
	if (!preg_match($hfu_pattern, $email)) {			
		// keine HFU E-Mail sondern nur Benutzername
		$email = $email . "@hs-furtwangen.de";			// Dies erlaubt beim Login-Namen den "@hs-furtwangen.de"-Teil auszulassen
	}
    // Das Benutzen vorbereiteter Statements verhindert SQL-Injektion.
    if ($stmt = $mysqli->prepare("SELECT id, password, salt FROM users WHERE email = ? LIMIT 1")) {
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
                    $_SESSION['login_string'] = hash('sha512', $password . $user_browser);
                    // Login erfolgreich.
                    return 0;
                } else {
                    // Passwort ist nicht korrekt
                    // Der Versuch wird in der Datenbank gespeichert
                    $now = time();
                    $mysqli->query("INSERT INTO login_attempts(user_id, time) VALUES ('$user_id', '$now')");
                    return 2;
                }
            }
        } else {
            //Es gibt keinen Benutzer.
            return 3;
        }
    }
}

/**
* Überprüft ob ein Konto 5 oder mehr erfolglose Login-Versuche in den letzten 2 Minuten hat
* 
* @author https://de.wikihow.com/Ein-sicheres-Login-Skript-mit-PHP-und-MySQL-erstellen
*
* @modified	no
*
* @param int	 $user_id	Eindeutige Benutzer-ID
* @param mysqli  $mysqli	Die zur Überprufung zu verwendende MYSQLi Verbindung
*
* @return bool
*/
function checkbrute($user_id, $mysqli) {
    // Hole den aktuellen Zeitstempel
    $now = time();

    // Alle Login-Versuche der letzten zwei Minuten werden gezählt.
    $valid_attempts = $now - (2 * 60 );

    if ($stmt = $mysqli->prepare("SELECT time FROM login_attempts WHERE user_id = ? AND time > '$valid_attempts'")) {
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

/**
* Überprüft ob ein Benutzer eingeloggt ist 
* 
* @author https://de.wikihow.com/Ein-sicheres-Login-Skript-mit-PHP-und-MySQL-erstellen
*
* @modified	yes
*
* @param mysqli  $mysqli	Die zur Überprufung zu verwendende MYSQLi Verbindung
*
* @return int	 $user_id	Die eindeutige Benutzer-ID, falls 0 zurück gegeben wird ist der Benutzer nicht eingeloggt
*/
function login_check($mysqli) {
    // Überprüfe, ob alle Session-Variablen gesetzt sind
    if (isset($_SESSION['user_id'], $_SESSION['login_string'])) {

        $user_id = $_SESSION['user_id'];
        $login_string = $_SESSION['login_string'];

        // Hole den user-agent string des Benutzers.
        $user_browser = $_SERVER['HTTP_USER_AGENT'];

        if ($stmt = $mysqli->prepare("SELECT password FROM users WHERE id = ? LIMIT 1")) {
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

/**
* Überprüft ob ein Benutzer Betreuer-Rechte hat
* 
* @author Tom Lehmann
*
* @param 	mysqli  $mysqli	Die zur Überprufung zu verwendende MYSQLi Verbindung
*
* @return 	int 	$level	Gibt das Betreuer-Level zurück
*/
function admin_check($mysqli) {	//überprüfe ob angemeldeter Benutzer höhere Rechte hat
	if (isset($_SESSION['user_id'])) {
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
}

/**
* Überprüft ob ein Benutzer-Account schon aktiviert ist
* 
* @author Tom Lehmann
*
* @param 	mysqli  $mysqli	Die zur Überprufung zu verwendende MYSQLi Verbindung
*
* @return 	bool 	$active	Gibt zurück ob ein Account aktiviert ist
*/
function active_check($mysqli) {	//überprüfe ob angemeldeter Benutzer einen aktivierten Account hat
	if (isset($_SESSION['user_id'])) {
		$user_id = $_SESSION['user_id'];
		if ($stmt = $mysqli->prepare("SELECT active FROM users WHERE id = ? LIMIT 1")) {
				$stmt->bind_param('i', $user_id);
				$stmt->execute();   // Führe das prepared-Statement aus
				$stmt->store_result();

				if ($stmt->num_rows == 1) {
					// Wenn es den Benutzer gibt, hole die Variablen von result.
					$stmt->bind_result($active);
					$stmt->fetch();
					return $active;
				}
			}
	}
}

/**
* Überprüft ob ein Benutzer Druck-Rechte hat
* 
* @author Tom Lehmann
*
* @param 	mysqli  $mysqli	Die zur Überprufung zu verwendende MYSQLi Verbindung
*
* @return 	bool 	$rights	Gibt die Druckrechte zurück
*/
function printrights_check($mysqli) {	//überprüfe ob angemeldeter Benutzer einen aktivierten Account hat
	if (isset($_SESSION['user_id'])) {
		$user_id = $_SESSION['user_id'];
		if ($stmt = $mysqli->prepare("SELECT rights FROM users WHERE id = ? LIMIT 1")) {
				$stmt->bind_param('i', $user_id);
				$stmt->execute();   // Führe das prepared-Statement aus
				$stmt->store_result();

				if ($stmt->num_rows == 1) {
					// Wenn es den Benutzer gibt, hole die Variablen von result.
					$stmt->bind_result($rights);
					$stmt->fetch();
					return $rights;
				}
			}
	}
}

/**
* Überprüft die Email-Adresse des Benutzers
* 
* @author Tom Lehmann
*
* @param 	mysqli  $mysqli	Die zur Überprufung zu verwendende MYSQLi Verbindung
*
* @return 	string 	$email	Gibt die Email zurück
*/
function email_check($mysqli) {	
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
			} else return false;
		}
	}
}


/**
* Überprüft ob es ein ID/Key Paar für die Authentifizierung beim 3D-Drucker gibt
* 
* @author Tom Lehmann
*
* @param 	mysqli  $mysqli	Die zur Überprufung zu verwendende MYSQLi Verbindung
*
* @return 	array 	
*/
function req_auth($mysqli) {
	$id = 1;	// falls irgendwann mehrere ID/Key Paare existieren (z.B. bei mehreren Druckern)
	if ($stmt = $mysqli->prepare("SELECT auth_id, auth_key FROM ultimaker_authentification WHERE id = ? LIMIT 1")) {
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


/**
* Funktion, die aufgerufen wird wenn kein ID/Key-Paar in der Datenbank gespeichert sein sollte
* 
* @return json	
*/
function require_auth($ulti, $app, $appuser) {
	$path = "/auth/request";
	$data = "application=" . $app . "&user=" . $appuser;
	
	$json = post($ulti, $path, $data);

print_r($json);	
	$id = json_decode($json)->{'id'};
	$key = json_decode($json)->{'key'};
	return array ( $id, $key );
}

/**
* Escaped einen URL-String
* 
* @author https://de.wikihow.com/Ein-sicheres-Login-Skript-mit-PHP-und-MySQL-erstellen
*
* @modified	no
*/
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
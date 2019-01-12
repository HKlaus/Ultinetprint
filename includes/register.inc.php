<?php
/**
* @author   Tom Lehmann & https://de.wikihow.com/Ein-sicheres-Login-Skript-mit-PHP-und-MySQL-erstellen
* @version  1.0
* 
*/

include_once 'db_connect.php';
include_once 'mail.php';
 
$error_msg = "";
 
/**
* Führt die Registrierung durch
* 
* @author https://de.wikihow.com/Ein-sicheres-Login-Skript-mit-PHP-und-MySQL-erstellen
*
* @modified	yes
*/
if (isset($_POST['email'], $_POST['p'])) {	
    // Bereinige und überprüfe die Daten
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
	
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // keine gültige E-Mail
        $error_msg .= '<p class="error">Die Email-Adresse ist ungültig.</p>';
    }
 
	$hfu_pattern = "/@hs-furtwangen\.de/";				// Mit diesem Regex-Ausdruck wird überprüft ob die Email eine gültige Endung hat
	if (!preg_match($hfu_pattern, $email)) {
		// keine HFU E-Mail
		$error_msg .= '<p class="error">Keine gültige HFU Email-Adresse.</p>';
	}
	
    $password = filter_input(INPUT_POST, 'p', FILTER_SANITIZE_STRING);
    if (strlen($password) != 128) {
        // Das gehashte Passwort sollte 128 Zeichen lang sein.
        // Wenn nicht, dann ist etwas sehr seltsames passiert
        $error_msg .= '<p class="error">Invalide Passwort Konfiguration.</p>';
    }
 
    // Das Passwort wurde auf der Benutzer-Seite schon überprüft.
    // Das sollte genügen, denn niemand hat einen Vorteil, wenn diese Regel   
    // verletzt wird.
	
    $prep_stmt = "SELECT id FROM users WHERE email = ? LIMIT 1";
    $stmt = $mysqli->prepare($prep_stmt);
 
    if ($stmt) {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
 
        if ($stmt->num_rows == 1) {
            // Ein Benutzer mit dieser E-Mail-Adresse existiert schon
            $error_msg .= '<p class="error">Ein Benutzer mit dieser Email-Adresse existiert bereits.</p>';
        }
    } else {
        $error_msg .= '<p class="error">Datenbank Fehler</p>';
    }
 
    if (empty($error_msg)) {
		$active = "0";	// Account standardmäßig deaktiviert bis Email Verifikation
		$level = "0";	// Betreuer-Level standardmäßig auf 0
		$rights = "0";	// Druck-Rechte standardmäßig auf 0
		
        // Erstelle ein zufälliges Salt
        $random_salt = hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE));
 
        // Erstelle saltet Passwort 
        $password = hash('sha512', $password . $random_salt);
 
        // Trage den neuen Benutzer in die Datenbank ein 
        if ($insert_stmt = $mysqli->prepare("INSERT INTO users (email, password, salt, active, level, rights) VALUES (?, ?, ?, ?, ?, ?)")) {
            $insert_stmt->bind_param('ssssss', $email, $password, $random_salt, $active, $level, $rights);
            // Führe die vorbereitete Anfrage aus.
            if (! $insert_stmt->execute()) {
                header('Location: ../error.php?err=Registrations Fehler: INSERT');
            }
        }
		send_verify($email, $password);			// Sende die Aktivierungsmail an $email und benutze den Passwort-Hash $password als Hash für den Aktivierungslink
    }
}

<?php
/**
* Index-Seite, wird aufgerufen wenn man die Website besucht
* Bietet die Möglichkeit sich einzuloggen
*
* @author   Tom Lehmann & https://de.wikihow.com/Ein-sicheres-Login-Skript-mit-PHP-und-MySQL-erstellen
* @version  1.0
* 
*/
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
 
sec_session_start();
 
include_once 'breadcrumbs/check_rights.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Login</title>
        <link rel="stylesheet" href="styles/main.css" />
        <link rel="stylesheet" href="styles/input.css" />
        <link rel="stylesheet" href="styles/index.css" />
        <script type="text/JavaScript" src="js/sha512.js"></script> 
        <script type="text/JavaScript" src="js/forms.js"></script> 
        <script type="text/JavaScript" src="js/cookie.js"></script> 
    </head>
    <body>
        <?php																			// Handling der Weiterleitung mittels GET-Paramter
        if (isset($_GET['success'])) {
			if ($_GET['success'] == 1) { echo '<div id="response">Registrierung erfolgreich! Logge dich nun ein:</div>'; }
		}
        if (isset($_GET['error'])) {
			if ($_GET['error'] == 1) { echo '<div id="response">Fehler beim einloggen: maximale Anzahl an Login Versuche (5)</div>'; }
			elseif ($_GET['error'] == 2) { echo '<div id="response">Fehler beim einloggen: Passwort inkorrekt!</div>'; }
			elseif ($_GET['error'] == 3) { echo '<div id="response">Fehler beim einloggen: Unbekannte Email-Adresse!</div>'; }
			elseif ($_GET['error'] == 4) { echo '<div id="response">Fehler beim einloggen: Account nicht aktiviert!</div>'; }
			else { echo '<div id="response">Unbekannter Fehler beim einloggen!</div>'; }
        } ?> 
		<?php include 'breadcrumbs/logged_in_as.php'; 
		if ($logged == "abgemeldet") { 													// Sofern Benutzer nicht eingeloggt ist, gib ihm die Möglichkeit
			echo "	<div id='background'>
					<form action='includes/process_login.php' method='post' name='login_form' id='login_form'>                      
						<input type='text' name='email' id='email' placeholder='Benutzer' />
						<br><input type='password' name='password' id='password' placeholder='Passwort' />
						<input type='button' value='Login' onclick='formhash(this.form, this.form.password); setCookie(this.form.email.value);' /> 
					</form>";
			include 'breadcrumbs/registrieren.php';
		} else if ($logged == "angemeldet") header('Location: ../printer.php');			// Sofern Benutzer eingeloggt, leite direkt auf die Druckerseite weiter
		?>
		<div id='impressum'><a href='impressum.php'>Impressum</a>.</div>
    </body>
</html>

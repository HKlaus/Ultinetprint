<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
 
sec_session_start();
 
if (login_check($mysqli) > 0) {
    $logged = 'angemeldet';
	header('Location: ../printer.php');
} else {
    $logged = 'abgemeldet';
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Login</title>
        <link rel="stylesheet" href="styles/main.css" />
        <script type="text/JavaScript" src="js/sha512.js"></script> 
        <script type="text/JavaScript" src="js/forms.js"></script> 
    </head>
    <body>
        <?php
        if (isset($_GET['success'])) {
			if ($_GET['success'] == 1) { echo '<div id="response">Registrierung erfolgreich! Logge dich nun ein</div>'; }
		}
        if (isset($_GET['error'])) {
			if ($_GET['error'] == 1) { echo '<div id="response">Fehler beim einloggen: maximale Anzahl an Login Versuche (5)</div>'; }
			elseif ($_GET['error'] == 2) { echo '<div id="response">Fehler beim einloggen: Passwort inkorrekt!</div>'; }
			elseif ($_GET['error'] == 3) { echo '<div id="response">Fehler beim einloggen: Unbekannte Email-Adresse!</div>'; }
			else { echo '<div id="response">Unbekannter Fehler beim einloggen!</div>'; }
        } ?> 
		<?php include 'breadcrumbs/logged_in_as.php'; ?>
		<div id="background">
        <form action="includes/process_login.php" method="post" name="login_form">                      
            Email: <input type="text" name="email" id="email"/>
            Passwort: <input type="password"
                             name="password" 
                             id="password"/>
            <input type="button"
                   value="Login" 
                   onclick="formhash(this.form, this.form.password);" /> 
        </form>
        <?php include 'breadcrumbs/registrieren.htm'; ?>
    </body>
</html>

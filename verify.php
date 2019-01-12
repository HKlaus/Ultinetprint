<?php
/**
* Seite der Email-Verifikation
*
* @author   Tom Lehmann & https://code.tutsplus.com/tutorials/how-to-implement-email-verification-for-new-members--net-3824
* @version  1.0
* 
*/

include_once 'includes/db_connect.php';
include_once 'includes/functions.php';

sec_session_start();

/**
* Prüft Email und Hash und aktiviert bei Korrektheit den Account
* 
* @author https://de.wikihow.com/Ein-sicheres-Login-Skript-mit-PHP-und-MySQL-erstellen
*
* @modified	yes
*/
if(isset($_GET['email']) && !empty($_GET['email']) AND isset($_GET['hash']) && !empty($_GET['hash'])){
	$email = mysqli_escape_string($mysqli, $_GET['email']); 
	$hash = mysqli_escape_string($mysqli, $_GET['hash']); 
	$search = mysqli_query($mysqli, "SELECT email, password, active FROM users WHERE email='".$email."' AND password='".$hash."' AND active='0'"); 
	$match  = mysqli_num_rows($search);
	if($match > 0){
		mysqli_query($mysqli, "UPDATE users SET active='1' WHERE email='".$email."' AND password='".$hash."' AND active='0'");
		echo "<div id='response'>Dein Account <b>". $email . "</b> wurde aktiviert</div>";
	} else {
		echo "<div id='response'><h1>Es ist ein Problem aufgetreten.</h1><p>Entweder ist der Link inkorrekt oder der Account wurde bereits aktiviert.</p></div>";
	}
} else {
	echo "<div id='response'><h1>Es ist ein Problem aufgetreten!</h1><p>Bitte nutzen Sie den Link aus der Aktivierungs-Email.</p></div>";
}
	
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Verifikation</title>
        <link rel="stylesheet" href="styles/main.css" />
    </head>
    <body>
	<?php include 'breadcrumbs/back_to_login.php'; ?>
    </body>
</html>

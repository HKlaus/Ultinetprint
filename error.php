<?php
/**
*
* @author   Tom Lehmann & https://de.wikihow.com/Ein-sicheres-Login-Skript-mit-PHP-und-MySQL-erstellen
* @version  1.0
* 
*/

/**
* Fehler Seite zum darstellen von Fehlern
* 
*/
$error = filter_input(INPUT_GET, 'err', $filter = FILTER_SANITIZE_STRING);
 
if (! $error) {
    $error = 'Oops! Ein unbekannter Fehler ist aufgetreten.';
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Fehler</title>
        <link rel="stylesheet" href="styles/main.css" />
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
    </head>
    <body>
		<div id="response">
			<h1>Es ist ein Problem aufgetreten</h1>
			<p class="error"><?php echo $error; ?></p>  
		</div>
		<div id='back_to_login'><a href="index.php">Zurück</a> oder <a href="includes/logout.php">Ausloggen</a>.</div>
    </body>
</html>

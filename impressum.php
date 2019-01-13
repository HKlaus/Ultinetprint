<?php
/**
* Seite der Druckerübersicht
*
* @author   Tom Lehmann
* @version  1.0
* 
*/

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Impressum</title>
        <link rel="stylesheet" href="styles/main.css" />
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
    </head>
    <body>
		<div id="logged_in">
			Impressum
		</div>
		<div id="background">
			<div class='line'>
				Erstellt von <img id='img' src='images/user.png'> Tom Lehmann, Hochschule Furtwangen University: Allgemeine Informatik WS18/19
			</div>
			<div class='line'>
				Im Rahmen der Bachelorthesis "Teilautomatisierung eines Schmelzschichtungs-3D-Druckers unter Berücksichtigung von Aspekten der Industrie 4.0"
			</div>
		</div>
		<?php include 'breadcrumbs/back_to_login.php'; ?>
	</body>
</html>
<?php
/**
* Seite der Druckauftragsverwaltung
*
* @author   Tom Lehmann
* @version  1.0
* 
*/

include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
include_once 'includes/format.php';
include_once 'includes/manage_db_functions.php';

sec_session_start();

include_once 'breadcrumbs/check_rights.php';
include_once 'includes/upload_to_serv.php';
include_once 'includes/manage_prints_functions.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Druckaufträge verwalten</title>
        <link rel="stylesheet" href="styles/main.css" />
        <link rel="stylesheet" href="styles/manage.css" />
        <link rel="stylesheet" href="styles/printer.css" />
        <link rel="stylesheet" href="styles/input.css" />
        <link rel="stylesheet" href="styles/loading.css" />
        <script type="text/JavaScript" src="js/priority_range.js"></script>
		<script type="text/JavaScript" src="js/loading.js"></script>
    </head>
    <body>
	<?php include 'breadcrumbs/logged_in_as.php'; ?>
	<?php include 'breadcrumbs/navigation.php'; ?>
	<div id="background">
	<?php 
		if ($printrighted) { echo "
			<div class='line'>
				<div class='left_value'>Datei hochladen</div>
				<div class='right_value'>";
			include 'breadcrumbs/upload_prints.php';
			echo "</div>
			</div>
			<div class='line'>
				<div class='left_value'></div>
				<div class='right_value'></div>
			</div>";
		} else echo "
			<div class='line'>
				<div class='left_value'></div>
				<div class='right_value'></div>
			</div>
			<div class='line'>
				<div class='left_value'>Du besitzt leider keine Rechte um eigene Drucke in Auftrag zu geben. <img src='images/achtung.png' id='img' /></div>
				<div class='right_value'>Lasse dich von einem Betreuer freischalten!</div>
			</div>"; 
	?>
		<div id="prints">
			<form action="<?php echo esc_url($_SERVER['PHP_SELF']); ?>"  method="post"  name="print_file" oninput="change_prio();">
				<div class='line'>
					<div class='left_value'>
						Druckauftrag einreihen
					</div>
					<div class='right_value'>
						Priorität: <output name="numerisch">niedrig</output><input type="range" name="priority" min="0" max="<?php 	// Falls Benutzer Betreuerrechte hat darf er höhere Prioritäten einstellen
						if($admin == 'angemeldet') { echo '4"'; } else { echo '2"'; } if(!$printrighted) { echo "disabled=\"disabled\""; }	?> value="1">	
					</div>
				</div>
				<?php show_files($mysqli);?>
			</form>
		</div>
	&nbsp;</div>
	
	<!-- Für Lade-Anzeige -->
	<div class="loading style-2" id="loading">Bitte warten, dies kann mehrere Minuten dauern..<div class="loading-wheel"></div></div>
	
	<?php include 'breadcrumbs/logout.php'; ?>
	</body>
</html>

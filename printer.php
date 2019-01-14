<?php
/**
* Seite der Druckerübersicht
*
* @author   Tom Lehmann
* @version  1.0
* 
*/

include_once 'includes/http.php';
include_once 'includes/ultimaker.php';
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
include_once 'includes/format.php';

sec_session_start();

include_once 'breadcrumbs/check_rights.php';

$ulti = new Ultimaker3($mysqli, "thesis", "webservice", "ultimaker3.informatik.hs-furtwangen.de");			// Initialisiere den Drucker

include_once 'includes/manage_db_functions.php';
include_once 'includes/manage_printer_functions.php';
include_once 'includes/manage_prints_functions.php';

// Falls der Drucker gerade den Druck vorbereitet, soll die Seite regelmäßig neu geladen werden
if (get_state($ulti) == "pre_print") header("Refresh:60");
else echo "asd";
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Drucker verwalten</title>
        <link rel="stylesheet" href="styles/main.css" />
        <link rel="stylesheet" href="styles/printer.css" />
        <link rel="stylesheet" href="styles/input.css" />
        <link rel="stylesheet" href="styles/loading.css" />
		<script type="text/JavaScript" src="js/time.js"></script>
		<script type="text/JavaScript" src="js/loading.js"></script>
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
    </head>
    <body>
		<?php include 'breadcrumbs/logged_in_as.php'; ?>
		<?php include 'breadcrumbs/navigation.php'; echo get_state($ulti);?>
		
		<div id="background">
				<div class='line'>
					<div class='left_value'>Drucker</div>
					<div class='right_value'><b><?php echo get_name($ulti); ?></b></div>
				</div>
				<div class='line'>
					<div class='left_value'><?php echo get_printhead($ulti); ?></div>
					<div class='right_value'><?php echo verify_auth($ulti); ?></div>
				</div>
				<div class='line'>
					<div id="webcam_container"><?php include 'breadcrumbs/webcam.php'; ?></div>
				</div>
				<div class='line'>
					<div class='left_value'>Drucker Status</div>
					<div class='right_value'><?php echo get_status($ulti); ?></div>
				</div>
				<div class='line'>
					<div class='left_value'></div>
					<div class='right_value'><?php echo get_printname($ulti); ?></div>
				</div>
				<div class='line'>
					<div class='left_value'></div>
					<div class='right_value'><?php echo format_state(get_state($ulti)); ?></div>
				</div>
				<div class='line'>
					<div class='left_value'></div>
					<div class='right_value'></div>
				</div>
			</div>
		</div>
		
		<!-- Für Lade-Anzeige -->
		<div class="loading style-2" id="loading">Wird geladen...<div class="loading-wheel"></div></div>
		
		<?php include 'breadcrumbs/footer.php'; ?>
		<?php include 'breadcrumbs/logout.php'; ?>
	</body>
</html>
<?php
include_once 'includes/http.php';
include_once 'includes/ultimaker.php';
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
include_once 'includes/upload_to_serv.php';

sec_session_start();

include_once 'breadcrumbs/check_rights.php';

$ulti = new Ultimaker3($mysqli);			// Initialisiere den Drucker

include_once 'includes/manage_db_functions.php';
include_once 'includes/manage_prints_functions.php';
include_once 'includes/manage_printer_functions.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Drucker verwalten</title>
        <link rel="stylesheet" href="styles/main.css" />
        <link rel="stylesheet" href="styles/printer.css" />
        <link rel="stylesheet" href="styles/input.css" />
		<script type="text/JavaScript" src="js/time.js"></script>
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
    </head>
    <body>
		<?php include 'breadcrumbs/logged_in_as.php'; ?>
		<?php include 'breadcrumbs/navigation.php'; ?>
		<div id="background">
				<div class='line'>
					<div class='left_value'>Drucker</div>
					<div class='right_value'><b><?php echo get_name($ulti); ?></b></div>
				</div>
				<div class='line'>
					<div class='left_value'></div>
					<div class='right_value'><?php echo verify_auth($ulti); ?></div>
				</div>
				<div class='line'>
					<div class='left_value'></div>
					<div class='right_value'></div>
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
					<div class='left_value'><?php echo format_state(get_state($ulti)); ?></div>
					<div class='right_value'><?php echo get_printprogress($ulti); ?></div>
				</div>
				<div class='line'>
					<div class='left_value'></div>
					<div class='right_value'></div>
				</div>
				<div class='line'>
					<div id="webcam_container"><?php include 'breadcrumbs/webcam.php'; ?></div>
				</div>
			<?php if ($printrighted == "berechtigt") { echo "
				<div class='line'>
					<div class='left_value'>Datei hochladen</div>
					<div class='right_value'><?php include 'breadcrumbs/upload_prints.php'; ?></div>
				</div>
				<div class='line'>
					<div class='left_value'></div>
					<div class='right_value'></div>
				</div>
				<div class='line'>
					<div class='left_value'>Verfügbare Dateien</div>
					<div class='right_value'><?php include 'breadcrumbs/available_prints.php'; ?> </div>
				</div>
				<div class='line'>
					<div class='left_value'></div>
					<div class='right_value'></div>
				</div>";
			} ?>
			</div>
		</div>
		<?php include 'breadcrumbs/footer.php'; ?>
		<?php include 'breadcrumbs/logout.htm'; ?>
	</body>
</html>
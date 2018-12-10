<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
include_once 'includes/manage_db_functions.php';
include_once 'includes/manage_prints_functions.php';

sec_session_start();

include_once 'breadcrumbs/check_rights.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Druckerqueue verwalten</title>
        <link rel="stylesheet" href="styles/main.css" />
        <link rel="stylesheet" href="styles/manage.css" />
        <link rel="stylesheet" href="styles/printer.css" />
        <script type="text/JavaScript" src="js/radiobutton.js"></script> 
    </head>
    <body>
	<?php include 'breadcrumbs/logged_in_as.php'; ?>
	<?php include 'breadcrumbs/navigation.php'; ?>
	<div id="background">
		<?php if ($next_print = get_next_print($mysqli)) echo "
			<div class='line'>
				<div class='left_value'>Nächster Druck</div>
				<div class='right_value'> 
					" . show_next_print($mysqli) . "
				</div>
			</div>"; 
		?>
		<div class='line'>
			<div class='left_value'></div>
			<div class='right_value'></div>
		</div>
		<div id="prints">
		
		<div class='line'>
			<div class='left_value'>Kriterien für die Auswahl des nächsten Drucks</div>
			<div class='right_value'>Zuerst gilt die höhere Priorität, danach Drucke die vor <br>17 Uhr fertig werden, ansonsten der längste Druck.</div>
		</div>
		<form action="<?php echo esc_url($_SERVER['PHP_SELF']); ?>"  method="post"  name="manage_prints">
			<?php show_prints($mysqli);?>
		</form>
		</div>
	&nbsp;</div>
	<?php include 'breadcrumbs/logout.htm'; ?>
	</body>
</html>

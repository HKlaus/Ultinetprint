<?php
/**
* Seite der Benutzerverwaltung
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
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Benutzer Verwalten</title>
        <link rel="stylesheet" href="styles/main.css" />
        <link rel="stylesheet" href="styles/manage.css" />
    </head>
    <body>
	<?php include 'breadcrumbs/logged_in_as.php'; ?>
	<?php include 'breadcrumbs/navigation.php'; ?>
	<div id="background">
		<div id="users">
		<form action="<?php echo esc_url($_SERVER['PHP_SELF']); ?>"  method="post"  name="manage_users">
			<?php show_users($mysqli);?>
		</form>
		</div>
	&nbsp;</div>
	<?php include 'breadcrumbs/logout.php'; ?>
	</body>
</html>

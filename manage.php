<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
include_once 'includes/manage_db_functions.php';

sec_session_start();

if (login_check($mysqli) > 0) {
    $logged = 'angemeldet';
} else {
    $logged = 'abgemeldet';
	header('Location: ../index.php');
}
if (admin_check($mysqli) > 0) {
	$admin = 'angemeldet';
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Datenbank Verwalten</title>
        <link rel="stylesheet" href="styles/main.css" />
        <link rel="stylesheet" href="styles/manage.css" />
        <script type="text/JavaScript" src="js/radiobutton.js"></script> 
    </head>
    <body>
	<?php include 'breadcrumbs/logged_in_as.php'; ?>
	<?php include 'breadcrumbs/navigation.php'; ?>
	<div id="background">
		<div id="prints">
		<form action="<?php echo esc_url($_SERVER['PHP_SELF']); ?>"  method="post"  name="manage_prints">
			<?php show_prints($mysqli);?>
			<div id="input"><input type="submit" value="Los!"></input></div>
		</form>
		</div>
		<div id="users">
		<form action="<?php echo esc_url($_SERVER['PHP_SELF']); ?>"  method="post"  name="manage_users">
			<?php show_users($mysqli);?>
			<div id="input"><input type="submit" value="Los!"></input></div>
		</form>
		</div>
	&nbsp;</div>
	&nbsp;</div>
	<?php include 'breadcrumbs/logout.htm'; ?>
	</body>
</html>

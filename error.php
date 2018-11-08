<?php
$error = filter_input(INPUT_GET, 'err', $filter = FILTER_SANITIZE_STRING);
 
if (! $error) {
    $error = 'Oops! Ein unbekannter Fehler ist aufgetreten.';
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Fehler</title>
        <link rel="stylesheet" href="styles/main.css" />
    </head>
    <body>
		<div id="response">
			<h1>Es ist ein Problem aufgetreten</h1>
			<p class="error"><?php echo $error; ?></p>  
		</div>
		<?php include 'breadcrumbs/back_to_login.php'; ?>
    </body>
</html>

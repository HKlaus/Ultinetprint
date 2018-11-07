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
        <title>Login: Fehler</title>
        <link rel="stylesheet" href="styles/main.css" />
    </head>
    <body>
        <h1>Es ist ein Problem aufgetreten</h1>
        <p class="error"><?php echo $error; ?></p>  
    </body>
</html>

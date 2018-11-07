<?php
include_once 'db_connect.php';
include_once 'functions.php';
 
sec_session_start(); // Die sichere Funktion um eine PHP-Sitzung zu starten.
 
if (isset($_POST['email'], $_POST['p'])) {
    $email = $_POST['email'];
    $password = $_POST['p']; // Das gehashte Passwort.
 
    if (login($email, $password, $mysqli) == 1) {
        // Login fehlgeschlagen 
        header('Location: ../index.php?error=1');
    } elseif (login($email, $password, $mysqli) == 2) {
        // Login fehlgeschlagen 
        header('Location: ../index.php?error=2');
    } elseif (login($email, $password, $mysqli) == 3) {
        // Login fehlgeschlagen 
        header('Location: ../index.php?error=3');
    } else {
        // Login erfolgreich 
        header('Location: ../printer.php');
	}
} else {
    // Die korrekten POST-Variablen wurden nicht zu dieser Seite geschickt. 
    echo 'Invalide Anfrage';
}

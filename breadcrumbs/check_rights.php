<?php			// Überprüfe ob der Benutzer angemeldet ist der die Seite besucht
if (login_check($mysqli) > 0) {
    $logged = 'angemeldet';
	if (active_check($mysqli) == 0) {		// Wenn Account noch nicht aktiviert ist, leite auf Fehler-Seite
		header("Location: ../error.php?err=Account noch nicht aktiviert. Überprüfe dein E-Mail Postfach.");
	}
} else {		// Wenn Benutzer überhaupt nicht angemeldet ist leite ihn wieder auf die Index Seite (Login)
    $logged = 'abgemeldet';
	if (!preg_match('/index\.php/', $_SERVER['REQUEST_URI'])) {
		header('Location: ../index.php');
	}
}
if (admin_check($mysqli) > 0) {		// Wenn Benutzer Betreuer-Rechte hat setzte Variable
	$admin = 'angemeldet';
} else {
	$admin = 'abgemeldet';
}

$printrighted = printrights_check($mysqli);		// Wenn Benutzer Druck-Rechte hat setzte Variable

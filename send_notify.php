<?php

include_once 'includes/db_connect.php';
include_once 'includes/mail.php';

$now = time();

if ($stmt = $mysqli->query("SELECT * FROM mails")) {
	// hole Variablen von result.
    while ($row = $stmt->fetch_row()){
		if ($row[2] < $now) {
			if ($stmt = $mysqli->prepare("DELETE FROM mails WHERE mails.id = ?")) {
				$stmt->bind_param('i', $row[0]);
				// Führe die vorbereitet Abfrage aus.
				if (!$stmt->execute()) {
					header('Location: ../error.php?err=Druck Fehler: DELETE to_print');
				}
				send_notify($row[3], $row[4], $row[5]);
			}
		}
		
	}
	// Free result 
	$stmt->close();
}
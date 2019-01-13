<?php
/**
* PHP-Skript das vom CRON-Job aufgerufen wird um Emails zu versenden
*
* @author   Tom Lehmann
* @version  1.0
* 
*/

include_once 'db_connect.php';
include_once 'mail.php';

$now = time();

if ($stmt = $mysqli->query("SELECT * FROM mails")) {
    while (!empty($row = $stmt->fetch_row())){											// Sofern Emails vorhanden sind,
		if ($row[2] < $now) {															// die vor "jetzt" hätten versendet werden sollen,
			if ($stmt = $mysqli->prepare("DELETE FROM mails WHERE mails.id = ?")) {		// versende sie und lösche sie aus der DB
				$stmt->bind_param('i', $row[0]);
				if (!$stmt->execute()) {
					header('Location: ../error.php?err=Druck Fehler: DELETE to_print');
				}
				send_notify($row[3], $row[4], $row[5]);
			}
		}	
	}
	$stmt->close();
}
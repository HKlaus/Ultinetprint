<?php

include_once 'includes/db_connect.php';
include_once 'includes/mail.php';

$now = time();
echo $now;

if ($stmt = $mysqli->query("SELECT * FROM mails")) {
	// hole Variablen von result.
    while ($row = $stmt->fetch_row()){
		if ($row[1] < $now) {
			echo $row[1];
			send_notify($row[2], $row[3], $row[4]);
		}
	}
	// Free result 
	$stmt->close();
}
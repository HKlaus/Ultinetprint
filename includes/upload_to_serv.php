<?php

$gcode_pattern = "/\.(.+)gcode$/";

if(!empty($_FILES['file_to_serv'])) {
	if (preg_match($gcode_pattern, $_FILES['file_to_serv'])) {
		$path = "uploads/gcode/";
		$path = $path . basename( $_FILES['file_to_serv']['name']);
		if(move_uploaded_file($_FILES['file_to_serv']['tmp_name'], $path)) {
			echo "<div id='response'>Die Datei <b>".  basename( $_FILES['file_to_serv']['name']) . "</b> wurde hochgeladen.</div>";
		} else{
			echo "<div id='response'>Ein Fehler ist beim Hochladen der Datei <b>".  basename( $_FILES['file_to_serv']['name']) . " nach <b>" . $path . "</b> aufgetreten! (Keine Datei ausgewählt?)</div>";
		}
	} else {
		echo "<div id='response'>Die Datei <b>".  basename( $_FILES['file_to_serv']['name']) . "</b> ist nicht im richtigen Format (*.gcode).</div>";
	}
}
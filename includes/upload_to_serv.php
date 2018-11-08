<?php

$gcode_pattern = "/\.gcode$/";		// Regex Pattern zum Feststellen ob die hochzuladende Datei wirklich eine .gcode Datei ist

if(!empty($_FILES['file_to_serv'])) {		// Wenn Upload-Form eine Datei enthält
	$file = $_FILES['file_to_serv']['name'];		
	if (preg_match($gcode_pattern, $file)) {		// Prüfe Datei Endung
		$path = "uploads/gcode/";			// Speicherort für druckbare Dateien
		$path = $path . basename($_FILES['file_to_serv']['name']);			
		if(move_uploaded_file($_FILES['file_to_serv']['tmp_name'], $path)) {	// Verschiebe Datei von temporären Ordner nach uploads/gcode/
			echo "<div id='response'>Die Datei <b>".  $file . "</b> wurde hochgeladen.</div>";
		} else{
			echo "<div id='response'>Ein Fehler ist beim Hochladen der Datei <b>".  $file . " nach <b>" . $path . "</b> aufgetreten! (Keine Datei ausgewählt?)</div>";
		}
	} else {
		echo "<div id='response'>Die Datei <b>".  $file . "</b> ist nicht im richtigen Format (*.gcode).</div>";
	}
}
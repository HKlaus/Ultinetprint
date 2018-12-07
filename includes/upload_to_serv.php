<?php

$gcode_pattern = "/\.gcode$/";		// Regex Pattern zum Feststellen ob die hochzuladende Datei wirklich eine .gcode Datei ist

if(!empty($_FILES['file_to_serv'])) {		// Wenn Upload-Form eine Datei enthält
	if ($printrighted) {		// Prüfe auf Druckrechte
		$file = $_FILES['file_to_serv']['name'];		
		if (preg_match($gcode_pattern, $file)) {	// Prüfe Datei Endung
			$path = "uploads/gcode/";			// Speicherort für druckbare Dateien
			$path = $path . basename($_FILES['file_to_serv']['name']);	
			if(move_uploaded_file($_FILES['file_to_serv']['tmp_name'], $path)) {	// Verschiebe Datei von temporären Ordner nach uploads/gcode/
				$file_header = file_get_contents($path, FALSE, NULL, 0, 1000);		// Extrahiere die Druckdauer, lies dafür die ersten 1000 Zeichen ein
				if (!empty($file_header)) {
						$print_time = strstr($file_header, ';PRINT.TIME:', false);		// Suche nach der Kopf-Zeile der Druckdauer
						$print_time = substr($print_time, 12);			
						$print_time = substr($print_time, 0, strpos($print_time, ";"));		// Beschneide den String auf die eigentliche Dauer
				} else echo "<div id='response'>Fehler beim ermitteln der Druckdauer!</div>";
				insert_file($mysqli, $_SESSION['user_id'], $file, $print_time);
			} else{
				echo "<div id='response'>Ein Fehler ist beim Hochladen der Datei <b>".  $file . " nach <b>" . $path . "</b> aufgetreten! (Keine Datei ausgewählt?)</div>";
			}
		} else {
			echo "<div id='response'>Die Datei <b>".  $file . "</b> ist nicht im richtigen Format (*.gcode).</div>";
		}
	} else echo "<div id='response'>Du besitzt keine Druckrechte.</div>";
}
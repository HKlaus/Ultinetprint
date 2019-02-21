<?php
/**
*
* @author   Tom Lehmann
* @version  1.0
* 
*/

$gcode_pattern = "/\.gcode$/";		// Regex Pattern zum Feststellen ob die hochzuladende Datei wirklich eine .gcode Datei ist

/**
* Wird ausgelöst wenn das Upload-Forumlar eine Datei enthält
* 
*/
if(!empty($_FILES['file_to_serv'])) {
	if ($printrighted) {								// Prüfe auf Druckrechte
		$file = filter_var ($_FILES['file_to_serv']['name'], FILTER_SANITIZE_STRING);		// Escape Dateinamen
		if (strlen($file) < 50) {						// Dateiname zu lang
			if (preg_match($gcode_pattern, $file)) {	// Prüfe Datei Endung
				if (is_file_owner($mysqli, $_SESSION['user_id'], $file)) {
					$path = "uploads/gcode/";				// Speicherort für druckbare Dateien
					$path = $path . basename($file);	
					if(move_uploaded_file($_FILES['file_to_serv']['tmp_name'], $path)) {			// Verschiebe Datei von temporären Ordner nach uploads/gcode/
						$file_header = file_get_contents($path, FALSE, NULL, 0, 1000);				// Extrahiere die Druckdauer, lies dafür die ersten 1000 Zeichen ein
						if (!empty($file_header)) {
							$is_target_machine = preg_match("/Ultimaker 3/", $file_header);			// Prüfe ob im Dateiheader der Ultimaker 3 als Zieldrucker angegeben ist
							if ($is_target_machine) {
								$print_time = strstr($file_header, ';PRINT.TIME:', false);			// Suche nach der Kopf-Zeile der Druckdauer
								$print_time = substr($print_time, 12);			
								$print_time = substr($print_time, 0, strpos($print_time, ";"));		// Beschneide den String auf die eigentliche Dauer
								
								insert_file($mysqli, $_SESSION['user_id'], $file, $print_time);		// Füge die Datei in die DB ein
							} else echo "<div id='response'>Fehler: Die Datei scheint nicht für den Ultimaker 3 geeignet zu sein oder sie wurde mit einer zu alten Version von Cura erstellt.</div>";
						} else echo "<div id='response'>Fehler beim ermitteln der Druckdauer!</div>";
					} else{
						echo "<div id='response'>Ein Fehler ist beim Hochladen der Datei <b>".  $file . " nach <b>" . $path . "</b> aufgetreten! (Keine Datei ausgewählt?)</div>";
					}
				} else echo "<div id='response'>Fehler: Du bist nicht der Besitzer dieser Datei (gleicher Name).</div>";
			} else {
				echo "<div id='response'>Die Datei <b>".  $file . "</b> ist nicht im richtigen Format (*.gcode).</div>";
			}
		} else echo "<div id='response'>Der Dateiname ist zu lang (maximal 50 Zeichen).</div>";
	} else echo "<div id='response'>Du besitzt keine Druckrechte.</div>";
}
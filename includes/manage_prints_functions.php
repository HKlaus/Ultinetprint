<?php

if(!empty($_POST['file_to_print'])) {
	if(!empty($_POST['priority'])) {
		if($admin != 'angemeldet' and $_POST['priority'] > 2) {		// überprüfe ob Benutzer überhaupt berechtigt ist um höchste Priorität zu fordern
			$priority = 0;
		}
		$priority = $_POST['priority'];		// mit Priorität drucken?
	} else { 
		$priority = 1;
	}
	$file_header = file_get_contents("uploads/gcode/" . $_POST['file_to_print'], FALSE, NULL, 0, 1000);		// Extrahiere die Druckdauer, lies dafür die ersten 1000 Zeichen ein
	if (!empty($file_header)) {
			$print_time = strstr($file_header, ';PRINT.TIME:', false);		// Suche nach der Kopf-Zeile der Druckdauer
			$print_time = substr($print_time, 12);			
			$print_time = substr($print_time, 0, strpos($print_time, ";"));		// Beschneide den String auf die eigentliche Dauer
	}
	insert_print($mysqli, $_SESSION['user_id'], $_POST['file_to_print'], $priority, $print_time);
	reorder_prints($mysqli);
	//echo $file_header;
	//echo $ulti->post_file($_POST['file_to_print']);
}

function is_printing($ulti) {		// ist der Drucker gerade am Drucken?
	if (json_decode($ulti->get("print_job/name"))->{'message'} != "Not found") {
		echo "<div id='response'>Die Datei <b>".  $ulti->get("print_job/name") . "</b> wird nun gedruckt.</div>";
	} else {
		echo "<div id='response'><b>Fehler</b> beim Drucken.</div>";
	}
}
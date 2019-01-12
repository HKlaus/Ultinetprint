<?php
/**
*
* @author   Tom Lehmann
* @version  1.0
* 
*/

/**
* Überprüft ob der Benutzer versucht einen Druckauftrag in die Warteschlange einzureihen
* 
*/
if(!empty($_POST['printfile'])) {
	if ($printrighted) {												// Überprüfe ob Benutzer überhaupt Druckrechte hat
		if(!empty($_POST['priority'])) {								// Überprüfe ob Benutzer überhaupt eine Priorität gewählt hat
			if($admin != 'angemeldet' and $_POST['priority'] > 2) {		// überprüfe ob Benutzer überhaupt berechtigt ist um höchste Priorität zu fordern
				$priority = 0;											// Ansosten setze auf niedrigste
			}
			$priority = $_POST['priority'];								
		} else { 
			$priority = 0;												// Falls fehlen sollte setze normale Priorität
		}
		
		insert_print($mysqli, $_SESSION['user_id'], $_POST['printfile'], $priority);
	} else echo "<div id='response'><b>Fehler</b> beim Druckauftrag aufgeben: Keine Rechte.</div>";
}

/**
* Überprüft ob der Benutzer den "Play"-Knopf gedrückt hat um den nächsten Druck in der Warteschlange zu starten
* 
*/
if (isset($_POST['start_button'])) {		
	if ($next_print = get_next_print($mysqli)) {																			// Sofern es überhaupt einen nächsten Druck gibt
		if (!empty($next_print)) {
			$ulti->put("printer/led", json_encode(array('brightness' => '100', 'hue' => '100', 'saturation' => '100')));	// Ändere die Farbe der LEDs im Drucker auf Grün
			if ($stmt = $mysqli->prepare("UPDATE current_state SET `value` = ? WHERE `key` = 'current_print_id'")) {		// Setze die Informationen über den aktuellen Druck in der DB
				$stmt->bind_param('s', $next_print[8]);
				if (!$stmt->execute()) {
					header('Location: ../error.php?err=Druck Fehler: UPDATE current_print_id');
				}
				if ($stmt = $mysqli->prepare("UPDATE current_state SET `value` = ? WHERE `key` = 'current_print_file'")) {
					$stmt->bind_param('s', $next_print[3]);
					if (!$stmt->execute()) {
						header('Location: ../error.php?err=Druck Fehler: UPDATE current_print_file');
					}
					if ($stmt = $mysqli->prepare("UPDATE current_state SET `value` = ? WHERE `key` = 'current_print_owner'")) {
						$stmt->bind_param('s', $next_print[0]);
						if (!$stmt->execute()) {
							header('Location: ../error.php?err=Druck Fehler: UPDATE current_print_owner');
						}
						if ($stmt = $mysqli->prepare("DELETE FROM to_print WHERE to_print.id = ?")) {						// Entferne den Druck aus der Warteschlange
							$stmt->bind_param('i', $next_print[8]);
							if (!$stmt->execute()) {
								header('Location: ../error.php?err=Druck Fehler: DELETE to_print');
							}
							if($ulti->post_file($next_print[3])) {														// Wenn der Druck korrekt gestartet wird, erstelle Emails in DB
								$expected_finish = time() + floatval($next_print[5]) + 60 * 3;							// 60 * 3 simuliert die 3 Minuten die der Drucker ca. benötigt um Aufzuheizen
								new_mail($mysqli, $next_print[8], time(), $next_print[0], $next_print[3], " durch den Benutzer " . $ulti->get_user()->get_user_email() . " gestartet, erwartete Fertigstellung: ca. " . date("H:i \U\h\\r\, \a\m d.m", $expected_finish));
								new_mail($mysqli, $next_print[8], $expected_finish, $next_print[0], $next_print[3], "fertiggestellt");
								if (!empty($over_next_print = get_next_print($mysqli))) {									// Wenn es einen übernächsten Druck gibt, erstelle für den Besitzer eine Benachrichtigung
									new_mail($mysqli, $next_print[8], $expected_finish, $over_next_print[0], $over_next_print[3], "druckbereit");
								}
							}
						}
					}
				}
			}
		} else echo "<div id='response'>Kein Druckauftrag in der Warteschlange.</div>";
	}
}

/**
* Überprüft ob der Benutzer den "Stop"-Knopf gedrückt hat um den aktuellen Druck abzubrechen
* 
*/
if (isset($_POST['stop_button'])) {			
	if ($stmt = $mysqli->query("SELECT * FROM current_state")) {
		while ($row = $stmt->fetch_row()){																				// Wenn es einen aktuellen Druck gibt
			if ($row[0] == "current_print_id") $current_print_id = $row[1];
			if ($row[0] == "current_print_file") $current_print_file = $row[1];
			if ($row[0] == "current_print_owner") $current_print_owner = $row[1];
		}
		$ulti->put("printer/led", json_encode(array('brightness' => '100', 'hue' => '0', 'saturation' => '100')));		// Setze LEDs des Druckers auf rot
		delete_mail($mysqli, $row[1]); 																					// Entferne alle Emails aus der DB
		new_mail($mysqli, $current_print_id, time(), $current_print_owner, $current_print_file, " wurde durch den Benutzer " . $ulti->get_user()->get_user_email() . " abgebrochen.");			
	}																													// Erstelle neue Benachrichtigung über Abbruch
	if (empty($ulti->put("print_job/state", json_encode(array('target'=>"abort"))))) echo "<div id='response'>Der Druckauftrag wurde vorzeitig abgebrochen.</div>";
	else echo "<div id='response'>Der Druckauftrag wurde bereits abgebrochen.</div>";									// Falls Druckauftrag bereits abgebrochen
}

/**
* Gibt den aus dem Algorithmus berechneten nächsten Druck zurück
*
* @param mysqli	 $mysqli 	Die zu verwendende Datenbankverbindung für die MySQL Abfragen
*
* @return array
* 
*/
function get_next_print($mysqli) {
	$stmt = $mysqli->query("SELECT users.email, to_print.file_id, available_prints.id, available_prints.file_name, to_print.priority, available_prints.print_time, to_print.time, to_print.user_id, to_print.id 
							FROM to_print 
							INNER JOIN users ON to_print.user_id=users.id 
							INNER JOIN available_prints on available_prints.id=to_print.file_id
							ORDER BY to_print.priority DESC, cast(available_prints.print_time AS unsigned) ASC");
	$row = $stmt->fetch_row();
	if (date("G", time() + floatval($row[5])) < 17) {			// sollte der Druck mit der kürzesten Druckdauer vor 17 Uhr fertig werden, nehme diesen
		return $row;
	}
	$stmt = $mysqli->query("SELECT users.email, to_print.file_id, available_prints.id, available_prints.file_name, to_print.priority, available_prints.print_time, to_print.time, to_print.user_id, to_print.id 
							FROM to_print 
							INNER JOIN users ON to_print.user_id=users.id 
							INNER JOIN available_prints on available_prints.id=to_print.file_id
							ORDER BY to_print.priority DESC, cast(available_prints.print_time AS unsigned) DESC");
	$row = $stmt->fetch_row();									// Ansonsten nehme Druck der am längsten dauert
	return $row;
}

/**
* Gibt den aus dem Algorithmus berechneten nächsten Druck als formatierten String zurück
*
* @param mysqli	 $mysqli 	Die zu verwendende Datenbankverbindung für die MySQL Abfragen
*
* @return string
* 
*/
function show_next_print($mysqli) {
	if (!($next_print = get_next_print($mysqli))) {
		return "";
	}
	return "<img id='img' src='images/user.png' alt='' title='als Benutzer'> " . 	// Benutzer-Icon
	substr(substr($next_print[0], 0, strpos($next_print[0], "@")), 0, 54) 			// Name des Auftraggeber
	. "&nbsp;&nbsp;<img id='img' src='images/datei.png' alt='' title='Datei'> " .	// Datei-Icon
	substr(substr($next_print[3], 0, strpos($next_print[3], ".gcode")), 0, 32) 		// Gib den Dateinamen des nächsten Druckes aus (maximal 54 Zeichen) ohne .gcode Endung
	. "&nbsp;&nbsp;<img id='img' src='images/uhr.png' alt='' title='Dauer'> " . 	// Uhr-Icon
	seconds_to_time($next_print[5]);		  										// Druckdauer
}

/**
* Gibt die aus dem Algorithmus berechnete früheste Fertigstellung des nächsten Drucks zurück
*
* @param mysqli	 $mysqli 	Die zu verwendende Datenbankverbindung für die MySQL Abfragen
*
* @return string
* 
*/
function calculate_next_print_finish($mysqli) {
	return date("H:i \a\m d.m", time() + floatval(get_next_print($mysqli)[5]));
}
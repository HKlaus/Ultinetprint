<?php

if(!empty($_POST['printfile'])) {
	if ($printrighted) {
		if(!empty($_POST['priority'])) {
			if($admin != 'angemeldet' and $_POST['priority'] > 2) {		// überprüfe ob Benutzer überhaupt berechtigt ist um höchste Priorität zu fordern
				$priority = 0;
			}
			$priority = $_POST['priority'];		// mit Priorität drucken?
		} else { 
			$priority = 0;		// Falls fehlen sollte setze normale Priorität
		}
		
		insert_print($mysqli, $_SESSION['user_id'], $_POST['printfile'], $priority);
	} else echo "<div id='response'><b>Fehler</b> beim Druckauftrag aufgeben: Keine Rechte.</div>";
}

if (isset($_POST['start_button'])) {		// Starte den nächsten Druck
	$ulti->put("printer/led", json_encode(array('brightness' => '100', 'hue' => '100', 'saturation' => '100')));
	if ($stmt = $mysqli->query("SELECT to_print.file_id, available_prints.id, available_prints.file_name, available_prints.print_time, users.email, to_print.id
								FROM to_print 
								INNER JOIN available_prints on available_prints.id=to_print.file_id
								INNER JOIN users on users.id=to_print.user_id
								ORDER BY to_print.priority DESC, available_prints.time ASC")) {
		$next_print = $stmt->fetch_row();
		if (!empty($next_print)) {
			if ($stmt = $mysqli->prepare("UPDATE current_state SET `value` = ? WHERE `key` = 'current_print_id'")) {
				$stmt->bind_param('i', $next_print[5]);
				// Führe die vorbereitet Abfrage aus.
				if (!$stmt->execute()) {
					header('Location: ../error.php?err=Druck Fehler: UPDATE current_print_id');
				}
				if ($stmt = $mysqli->prepare("UPDATE current_state SET `value` = ? WHERE `key` = 'current_print_file'")) {
					$stmt->bind_param('s', $next_print[2]);
					// Führe die vorbereitet Abfrage aus.
					if (!$stmt->execute()) {
						header('Location: ../error.php?err=Druck Fehler: UPDATE current_print_file');
					}
					if ($stmt = $mysqli->prepare("UPDATE current_state SET `value` = ? WHERE `key` = 'current_print_owner'")) {
						$stmt->bind_param('s', $next_print[4]);
						// Führe die vorbereitet Abfrage aus.
						if (!$stmt->execute()) {
							header('Location: ../error.php?err=Druck Fehler: UPDATE current_print_owner');
						}
						if ($stmt = $mysqli->prepare("DELETE FROM to_print WHERE to_print.id = ?")) {
							$stmt->bind_param('i', $next_print[5]);
							// Führe die vorbereitet Abfrage aus.
							if (!$stmt->execute()) {
								header('Location: ../error.php?err=Druck Fehler: DELETE to_print');
							}
							new_mail($mysqli, $next_print[5], time(), $next_print[4], $next_print[2], "gestaret");
							new_mail($mysqli, $next_print[5], time() + floatval($next_print[3]), $next_print[4], $next_print[2], "fertiggestellt");
						}
					}
				}
			}
		} else echo "<div id='response'>Kein Druckauftrag in der Warteschlange.</div>";
		//$ulti->post_file($next_print[2]);
	}
}
if (isset($_POST['stop_button'])) {			// Stoppe momentanen Druck
	$ulti->put("printer/led", json_encode(array('brightness' => '100', 'hue' => '0', 'saturation' => '100')));
	if ($stmt = $mysqli->query("SELECT * FROM current_state")) {
		while ($row = $stmt->fetch_row()){
			if ($row[0] == "current_print_id") $current_print_id = $row[1];
			if ($row[0] == "current_print_file") $current_print_file = $row[1];
			if ($row[0] == "current_print_owner") $current_print_owner = $row[1];
		}
		delete_mail($mysqli, $row[1]); 
		new_mail($mysqli, $current_print_id, time(), $current_print_owner, $current_print_file, "abgebrochen");
	}
	if (empty($ulti->put("print_job/state", json_encode(array('target'=>"abort"))))) echo "<div id='response'>Der Druckauftrag wurde vorzeitig abgebrochen.</div>";
	else echo "<div id='response'>Der Druckauftrag wurde bereits abgebrochen.</div>";
}

function get_next_print($mysqli) {
	$stmt = $mysqli->query("SELECT users.email, to_print.file_id, available_prints.id, available_prints.file_name, to_print.priority, available_prints.print_time, to_print.time, to_print.user_id 
							FROM to_print 
							INNER JOIN users ON to_print.user_id=users.id 
							INNER JOIN available_prints on available_prints.id=to_print.file_id
							ORDER BY to_print.priority DESC, cast(available_prints.print_time AS unsigned) ASC");
	$row = $stmt->fetch_row();
	if (date("G", time() + floatval($row[5])) < 17) {			// sollte der Druck mit der kürzesten Druckdauer vor 17 Uhr fertig werden, nehme diesen
		return $row;
	}
	$stmt = $mysqli->query("SELECT users.email, to_print.file_id, available_prints.id, available_prints.file_name, to_print.priority, available_prints.print_time, to_print.time, to_print.user_id 
							FROM to_print 
							INNER JOIN users ON to_print.user_id=users.id 
							INNER JOIN available_prints on available_prints.id=to_print.file_id
							ORDER BY to_print.priority DESC, cast(available_prints.print_time AS unsigned) DESC");
	$row = $stmt->fetch_row();							// Ansonsten nehme Druck der am längsten dauert
	return $row;
}

function show_next_print($mysqli) {
	if (!($next_print = get_next_print($mysqli))) {
		return "";
	}
	return "<img id='img' src='images/user.png' alt='' title='als Benutzer'> " . // Icon
	substr(substr($next_print[0], 0, strpos($next_print[0], "@")), 0, 54) // Auftraggeber
	. "&nbsp;&nbsp;<img id='img' src='images/datei.png' alt='' title='Datei'> " .	// Icon
	substr(substr($next_print[3], 0, strpos($next_print[3], ".gcode")), 0, 32) 		// Gib den Dateinamen des nächsten Druckes aus (maximal 54 Zeichen) ohne .gcode Endung
	. "&nbsp;&nbsp;<img id='img' src='images/uhr.png' alt='' title='Dauer'> " . 	// Icon
	seconds_to_time($next_print[5]);		  // Druckdauer
}

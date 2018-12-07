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
		if ($stmt = $mysqli->prepare("UPDATE current_state SET `value` = ? WHERE `key` = 'current_print'")) {
			$stmt->bind_param('s', $next_print[2]);
			// Führe die vorbereitet Abfrage aus.
			if (!$stmt->execute()) {
				header('Location: ../error.php?err=Druck Fehler: UPDATE current_print');
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
					if ($insert_stmt = $mysqli->prepare("INSERT INTO mails (`at`, `to`, `file`, `event`) VALUES (?, ?, ?, ?)")) {
						$at = time() + $next_print[3];
						$to = $next_print[4];
						$file = $next_print[2];
						$event = "fertiggestellt.";
						$insert_stmt->bind_param('isss', $at, $to, $file, $event);
						// Führe die vorbereitete Anfrage aus.
						if (! $insert_stmt->execute()) {
							header('Location: ../error.php?err=Druck Fehler: INSERT mails');
						}
					}
				}
			}
		}
		//$ulti->post_file($next_print[2]);
	}
}
if (isset($_POST['stop_button'])) {			// Stoppe momentanen Druck
	$ulti->put("printer/led", json_encode(array('brightness' => '100', 'hue' => '0', 'saturation' => '100')));
	if (empty($ulti->put("print_job/state", json_encode(array('target'=>"abort"))))) echo "<div id='response'>Der Druckauftrag wurde vorzeitig abgebrochen.</div>";
	else echo "<div id='response'>Der Druckauftrag wurde bereits abgebrochen.</div>";
}
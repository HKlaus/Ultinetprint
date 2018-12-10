<?php

//
//	Stelle Datenbank dar

function show_users($mysqli) {
	// gibt die Tabellenbeschriftung aus
	echo "<div class='row'><div class='inline' id='id'><b>#</b>
	</div><div class='inline' id='active'><b>Aktiv</b>
	</div><div class='inline' id='email'><b>Email</b>
	</div><div class='inline' id='level'><b>Betreuer?</b>
	</div><div class='inline' id='user_action'><b>Druckrechte</b>
	</div></div>";
    if ($stmt = $mysqli->query("SELECT id, email, level, active, rights FROM users")) {
        // hole Variablen von result.
        while ($row = $stmt->fetch_row()){
			$level_img = "<img src='images/rang_hoch.png' alt='Nein' title='Zum Betreuer befördern'>";
			$rights_img = "<img src='images/löschen.png' alt='Nein' title='Druck Rechte erteilen'>";
			$active_img = "<img src='images/inaktiv.png' alt='Inaktiv' title='Account akvitieren'>";
			if ($row[2] > 0) { 
				$level_img = "<img src='images/erfolg.png' alt='Ja' title='Betreuer Rechte entziehen'>";
			}
			if ($row[3] > 0) {
				$active_img = "<img src='images/erfolg.png' alt='Aktiv' title='Account deakvitieren'>";
			}
			if ($row[4] > 0) { 
				$rights_img = "<img src='images/erfolg.png' alt='Ja' title= 'Druck Rechte entziehen'>";
			}
			// gebe Benutzer zeilenweiße aus 
			echo "<div class='row'><div class='inline' id='id'>" . $row[0] . 
				 "</div><div class='inline' id='active'><label for='active" . $row[0] . "'>" . $active_img . "</label>
				  <input class='user_button' type='submit' name='active' id='active" . $row[0] . "' value='" .  $row[0] . "'>
			      </div><div class='inline' id='email'>" . $row[1] . 
				 "</div><div class='inline' id='level'><label for='level" . $row[0] . "'>". $level_img . "</label>
				  <input class='user_button' type='submit' name='level' id='level" . $row[0] . "' value='" .  $row[0] . "'>
				  </label></div><div id='user_action' class='inline'>
				  <label for='rights" . $row[0] . "'>". $rights_img . "</label>
				  <input class='user_button' type='submit' name='userrights' id='rights" . $row[0] . "' value='" .  $row[0] . "'></div></div>";
		}
		// Free result 
		$stmt->close();
	}
}

function show_files($mysqli) {		// Zeige alle druckbaren Dateien aus Warteschlange an
	// gibt die Tabellenbeschriftung aus
	echo "<div class='row'><div class='inline' id='user'><b>Von</b>
	</div><div class='inline' id='file_name'><b>Datei</b>
	</div><div class='inline' id='date'><b>Erstelldatum</b>
	</div><div class='inline' id='print_time'><b>Dauer</b>
	</div><div class='inline' id='print'>
	</div></div>";
    if ($stmt = $mysqli->query("SELECT users.email, available_prints.id, available_prints.file_name, available_prints.print_time, available_prints.time, available_prints.user_id FROM available_prints INNER JOIN users ON available_prints.user_id=users.id ORDER BY time DESC")) {
        // hole Variablen von result.
        while ($row = $stmt->fetch_row()){
			$print_img = "<img title='Druckauftrag einreihen' src='images/print.png' alt='Drucken'>";
			// gebe Aufträge zeilenweiße aus 
			echo "<div class='row'><div class='inline' id='user'>" . substr($row[0], 0, strpos($row[0], "@")) . 		// Gebe Benutzer ohne Email-Endung an
			     "</div><div class='inline' id='file_name'>" . substr($row[2], 0, strpos($row[2], ".gcode")) . 			// Gebe Dateiname ohne gcode-Endung an
				 "</div><div class='inline' id='date'>" . date("H:i \a\m d.m.Y", $row[4]) .
				 "</div><div class='inline' id='print_time'>" . seconds_to_time($row[3]) .
				 "</div>";
			if (printrights_check($mysqli)) {
				echo "<div class='inline right'>
				  <label for='printfile" . $row[1] . "'>". $print_img . "</label>
				  <input class='user_button' type='submit' name='printfile' id='printfile" . $row[1] . "' value='" .  $row[1] . "'></div>";
			}
			echo "</div>";
		}
		// Free result 
		$stmt->close();
	}
}

function show_prints($mysqli) {		// Zeige alle Druckaufträge aus Warteschlange an
	// gibt die Tabellenbeschriftung aus
	echo "<div class='row'><div class='inline' id='user'><b>Benutzer</b>
	</div><div class='inline' id='file_name'><b>Datei</b>
	</div><div class='inline' id='priority'><b>Priorität</b>
	</div><div class='inline' id='print_time'><b>Dauer</b>
	</div><div class='inline' id='delete'>
	</div></div>";
    if ($stmt = $mysqli->query("SELECT users.email, to_print.file_id, available_prints.id, available_prints.file_name, to_print.priority, available_prints.print_time, to_print.time, to_print.user_id, to_print.id 
								FROM to_print 
								INNER JOIN users ON to_print.user_id=users.id 
								INNER JOIN available_prints on available_prints.id=to_print.file_id
								ORDER BY to_print.priority DESC, cast(available_prints.print_time AS unsigned) ASC")) {
        // hole Variablen von result.
        while ($row = $stmt->fetch_row()){
			$delete_img = "<img title='Löschen' src='images/löschen.png' alt='Nein'>";
			$priority_img = "<img title='Priorität' src='images/stern2.png' alt='*'>";
			$priority_minus_img = "<img title='Priorität senken' src='images/minus.png' alt='-'>";
			$priority_plus_img = "<img title='Priorität erhöhen' src='images/plus.png' alt='+'>";
			if ($row[4] < 1) $row[4] = 0;
			// gebe Aufträge zeilenweiße aus 
			echo "<div class='row'><div class='inline' id='user'>" . substr($row[0], 0, strpos($row[0], "@")) . 		// Gebe Benutzer ohne Email-Endung an
			     "</div><div class='inline' id='file_name'>" . substr($row[3], 0, strpos($row[3], ".gcode")) . 			// Gebe Dateiname ohne gcode-Endung an
				 "</div><div class='inline' id='priority'>";
			if (admin_check($mysqli) > 0) {
				if ($row[4] > 0) {
					echo "<label for='prio_minus" . $row[8] . "'>". $priority_minus_img . "</label>" .
						 "<input class='user_button' type='submit' name='prio_minus' id='prio_minus" . $row[8] . "' value='" .  $row[8] . "'>";
				}
			}
			echo "<label>" . str_repeat($priority_img, $row[4]) . "</label>";
			if (admin_check($mysqli) > 0) {
				if ($row[4] < 6) {
					echo "<label for='prio_plus" . $row[8] . "'>". $priority_plus_img . "</label>
						  <input class='user_button' type='submit' name='prio_plus' id='prio_plus" . $row[8] . "' value='" .  $row[8] . "'>";
				}
			}
			echo "</div><div class='inline' id='print_time'>" . seconds_to_time($row[5]) .
				 "</div>";
			if (login_check($mysqli) == $row[7] or admin_check($mysqli) > 0) {
				echo "<div class='inline right'>
				  <label for='deleteprint" . $row[8] . "'>". $delete_img . "</label>
				  <input class='user_button' type='submit' name='deleteprint' id='deleteprint" . $row[8] . "' value='" .  $row[8] . "'></div>";
			}
			echo "</div>";
		}
		// Free result 
		$stmt->close();
	}
}

//
//	Ändere Datenbank

function change_rights($mysqli, $user_id) {		// Gebe ausgewähltem Benutzer Druckrechte
	if ($stmt = $mysqli->prepare("SELECT level FROM users WHERE id = ? LIMIT 1")) {
        $stmt->bind_param('i', $user_id);
		$stmt->execute();   // Execute the prepared query.
        $stmt->store_result();
		
		// hole Variable von result.
		if ($stmt->num_rows == 1) {
			$stmt->bind_result($user_level);
			$stmt->fetch();
			if ($user_level < 1) {
				if ($stmt = $mysqli->prepare("SELECT rights FROM users WHERE id = ? LIMIT 1")) {
					$stmt->bind_param('i', $user_id);
					$stmt->execute();   // Execute the prepared query.
					$stmt->store_result();
					
					// hole Variable von result.
					if ($stmt->num_rows == 1) {
						$stmt->bind_result($old_user_rights);
						$stmt->fetch(); 
						$new_user_rights = 0;		// Wenn Benutzer Rechte 1 hatte dann setze Rechte 0
						if ($old_user_rights == 0) { $new_user_rights = 1; } 	// Ansonsten setze Rechte auf 1
						if ($stmt = $mysqli->prepare("UPDATE users SET rights = ? WHERE id = ?")) {
							$stmt->bind_param('ii', $new_user_rights, $user_id);
							// Führe die vorbereitet Abfrage aus.
							if (!$stmt->execute()) {
								header('Location: ../error.php?err=Verwaltungs Fehler: UPDATE Rechte');
							}
						db_history($mysqli, "changed user rights", $user_id);			// trage in DB History Tabelle ein
						}
					}
					if ($new_user_rights == 1) {
						echo "<div id='response'>Dem Benutzer Nr. " . $user_id . " Druckrechte erteilt.</div>";
					} else {
						echo "<div id='response'>Dem Benutzer Nr. " . $user_id . " Druckrechte entzogen.</div>";
					}
				}
			} else echo "<div id='response'>Dem Benutzer Nr. " . $user_id . " können keine Druckrechte entzogen werden, da er ein Betreuer ist.</div>";
		}
	}
}
function change_user_level($mysqli, $user_id) {	// Ändere das Level (Benutzer oder Betreuer) des ausgewählten Benutzers
	if ($stmt = $mysqli->prepare("SELECT level FROM users WHERE id = ? LIMIT 1")) {
        $stmt->bind_param('i', $user_id);
		$stmt->execute();   // Execute the prepared query.
        $stmt->store_result();
		
		// hole Variable von result.
		if ($stmt->num_rows == 1) {
			$stmt->bind_result($old_user_level);
			$stmt->fetch();
		
			$new_user_level = 0;		// Wenn Benutzer Level 1 hatte dann setze Level 0
			if ($old_user_level == 0) { $new_user_level = 1; } 	// Ansonsten setze Level auf 1
			if ($stmt = $mysqli->prepare("UPDATE users SET level = ? WHERE id = ?")) {
				$stmt->bind_param('ii', $new_user_level, $user_id);
				// Führe die vorbereitet Abfrage aus.
				if (!$stmt->execute()) {
					header('Location: ../error.php?err=Verwaltungs Fehler: UPDATE Level');
				}
			db_history($mysqli, "changed user level", $user_id);			// trage in DB History Tabelle ein
			}
		}
		if ($new_user_level == 1) {
			echo "<div id='response'>Dem Benutzer Nr. " . $user_id . " Betreuerrechte erteilt.</div>";
		} else {
			echo "<div id='response'>Dem Benutzer Nr. " . $user_id . " Betreuerrechte entzogen.</div>";
		}
	}
}
function change_user_active($mysqli, $user_id) {	// Ändere ob Account des ausgewählten Benutzers aktiv ist
	if ($stmt = $mysqli->prepare("SELECT active FROM users WHERE id = ? LIMIT 1")) {
        $stmt->bind_param('i', $user_id);
		$stmt->execute();   // Execute the prepared query.
        $stmt->store_result();
		
		// hole Variable von result.
		if ($stmt->num_rows == 1) {
			$stmt->bind_result($old_user_active);
			$stmt->fetch();
		
			$new_user_active = 0;		// Wenn Benutzer aktiv war dann setze auf inaktiv
			if ($old_user_active == 0) { $new_user_active = 1; } 	// Ansonsten setze auf aktiv
			if ($stmt = $mysqli->prepare("UPDATE users SET active = ? WHERE id = ?")) {
				$stmt->bind_param('ii', $new_user_active, $user_id);
				// Führe die vorbereitet Abfrage aus.
				if (!$stmt->execute()) {
					header('Location: ../error.php?err=Verwaltungs Fehler: UPDATE Aktiv');
				}
			db_history($mysqli, "changed user active", $user_id);			// trage in DB History Tabelle ein
			}
		}
		if ($new_user_active == 1) {
			echo "<div id='response'>Account des Benutzer Nr. " . $user_id . " aktiviert.</div>";
		} else {
			echo "<div id='response'>Account des Benutzer Nr. " . $user_id . " deaktiviert.</div>";
		}
	}
}


//
//	Funktionen zur Druckauftragsverwaltung

function insert_file($mysqli, $user_id, $file_name, $print_time) {		// Füge neue Datei in "druckbare Dateien" ein
	$now = time();
	$stmt = $mysqli->prepare("DELETE FROM available_prints WHERE file_name = ?"); 			// Lösche den alten Eintrag aus der Tabelle da die Datei ersetzt wird
    $stmt->bind_param('s', $file_name);
	$stmt->execute();   // Execute the prepared query.
	if ($insert_stmt = $mysqli->prepare("INSERT INTO available_prints (user_id, file_name, print_time, time) VALUES (?, ?, ?, ?)")) {
        $insert_stmt->bind_param('isss', $user_id, $file_name, $print_time, $now);
        // Führe die vorbereitete Anfrage aus.
        if (! $insert_stmt->execute()) {
            header('Location: ../error.php?err=Druck Fehler: INSERT Datei');
        }
		db_history($mysqli, "inserted file " . $file_name, $user_id);			// trage in DB History Tabelle ein
		echo "<div id='response'>Datei ". $file_name . " hochgeladen.</div>";
    }
}
function insert_print($mysqli, $user_id, $file_id, $priority) {		// Trage Druckauftrag in Warteschlange ein
	$now = time();
	if ($insert_stmt = $mysqli->prepare("INSERT INTO to_print (user_id, file_id, priority, time) VALUES (?, ?, ?, ?)")) {
        $insert_stmt->bind_param('iiis', $user_id, $file_id, $priority, $now);
        // Führe die vorbereitete Anfrage aus.
        if (! $insert_stmt->execute()) {
            header('Location: ../error.php?err=Druck Fehler: INSERT Druck');
        }
		db_history($mysqli, "inserted print " . $file_id . " with priority " . $priority, $user_id);			// trage in DB History Tabelle ein
		echo "<div id='response'>Druck erfolgreich in die Warteschlange aufgenommen!</div>";
    }
}
function delete_print($mysqli, $print_id) {		// Lösche ausgewählten Auftrag
	if ($stmt = $mysqli->prepare("DELETE FROM to_print WHERE id = ?")) {
        $stmt->bind_param('i', $print_id);
		// Führe die vorbereitet Abfrage aus.
		if (! $stmt->execute()) {
            header('Location: ../error.php?err=Druck Fehler: DELETE Druck');
        }	
		db_history($mysqli, "deleted print", $print_id);			// trage in DB History Tabelle ein
		echo "<div id='response'>Druck Nr. " . $_POST['deleteprint'] . " gelöscht.</div>";
    }
}
function prio_plus($mysqli, $print_id) {		// Erhöhe Priorität des ausgewählten Auftrags
	if ($stmt = $mysqli->prepare("SELECT priority FROM to_print WHERE id = ?")) {		// Prüfe ob Priorität nicht schon maximal ist
        $stmt->bind_param('i', $print_id);
		$stmt->execute();   // Execute the prepared query.
        $stmt->store_result();
		
		// hole Variable von result.
		if ($stmt->num_rows == 1) {
			$stmt->bind_result($priority);
			$stmt->fetch();
			if ($priority < 6) {		// Falls noch größer Null -> erhöhe Priorität
				if ($stmt = $mysqli->prepare("UPDATE to_print SET priority = priority + 1 WHERE id = ?")) {
					$stmt->bind_param('i', $print_id);
					// Führe die vorbereitet Abfrage aus.
					if (! $stmt->execute()) {
						header('Location: ../error.php?err=Druck Fehler: Update Priorität plus');
					}	
					db_history($mysqli, "priority plus", $print_id);			// trage in DB History Tabelle ein
					echo "<div id='response'>Priorität erfoglreich erhöht.</div>";
				}
			} else echo "<div id='response'>Die Priorität ist schon die Höchste.</div>";
		}
	}
}
function prio_minus($mysqli, $print_id) {		// Senke Priorität des ausgewählten Auftrags
	if ($stmt = $mysqli->prepare("SELECT priority FROM to_print WHERE id = ?")) {		// Prüfe ob Priorität nicht schon Null ist
        $stmt->bind_param('i', $print_id);
		$stmt->execute();   // Execute the prepared query.
        $stmt->store_result();
		
		// hole Variable von result.
		if ($stmt->num_rows == 1) {
			$stmt->bind_result($priority);
			$stmt->fetch();
			if ($priority > 0) {		// Falls noch größer Null -> senke Priorität
				if ($stmt = $mysqli->prepare("UPDATE to_print SET priority = priority - 1 WHERE id = ?")) {
					$stmt->bind_param('i', $print_id);
					// Führe die vorbereitet Abfrage aus.
					if (! $stmt->execute()) {
						header('Location: ../error.php?err=Druck Fehler: Update Priorität minus');
					}	
					db_history($mysqli, "priority minus", $print_id);			// trage in DB History Tabelle ein
					echo "<div id='response'>Priorität erfoglreich gesenkt.</div>";
				}
			} else echo "<div id='response'>Die Priorität ist schon die Niedrigste.</div>";
		}
	}
}

// Verwalte Mails Funktionen

function new_mail($mysqli, $print_id, $at, $to, $file, $event) {
	if ($insert_stmt = $mysqli->prepare("INSERT INTO mails (`print_id`, `at`, `to`, `file`, `event`) VALUES (?, ?, ?, ?, ?)")) {
		$insert_stmt->bind_param('iisss', $print_id, $at, $to, $file, $event);
		// Führe die vorbereitete Anfrage aus.
		if (!$insert_stmt->execute()) {
			header('Location: ../error.php?err=Druck Fehler: INSERT mails');
		}
	}
}
function delete_mail($mysqli, $print_id) {
	if ($insert_stmt = $mysqli->prepare("DELETE FROM mails WHERE print_id = ?")) {
		$insert_stmt->bind_param('i', $print_id);
		// Führe die vorbereitete Anfrage aus.
		if (!$insert_stmt->execute()) {
			header('Location: ../error.php?err=Druck Fehler: DELETE mails');
		}
	}
}


//
//	Log Funktionen

function printer_history($mysqli, $user_id, $request, $path, $data="NULL") {
	$now = time();
	// Logge alle HTTP post & put Anfragen
	if ($insert_stmt = $mysqli->prepare("INSERT INTO printer_history (user_id, request, time, path, data) VALUES (?, ?, ?, ?, ?)")) {
        $insert_stmt->bind_param('issss', $user_id, $request, $now, $path, $data);
        // Führe die vorbereitete Anfrage aus.
        if (!$insert_stmt->execute()) {
            header('Location: ../error.php?err=Logging Fehler: INSERT Printer Log');
        }
    }
}
function db_history($mysqli, $action, $data) {
	$now = time();
	// Logge alle Benutzeraktionen
	if ($insert_stmt = $mysqli->prepare("INSERT INTO db_history (action, time, data) VALUES (?, ?, ?)")) {
        $insert_stmt->bind_param('sss', $action, $now, $data);
        // Führe die vorbereitete Anfrage aus.
        if (!$insert_stmt->execute()) {
            header('Location: ../error.php?err=Logging Fehler: INSERT DB Log');
        }
    }
}


if (isset($_POST['active'])) {
	change_user_active($mysqli, $_POST['active']);
}
if (isset($_POST['level'])) {
	change_user_level($mysqli, $_POST['level']);
}
if (isset($_POST['userrights'])) {
	change_rights($mysqli, $_POST['userrights']);
}
if (isset($_POST['deleteprint'])) {
	delete_print($mysqli, $_POST['deleteprint']);
}
if (isset($_POST['prio_minus'])) {
	prio_minus($mysqli, $_POST['prio_minus']);
}
if (isset($_POST['prio_plus'])) {
	prio_plus($mysqli, $_POST['prio_plus']);
}
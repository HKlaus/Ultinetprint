<?php

//
//	Stelle Datenbank dar

function show_users($mysqli) {
	// gibt die Tabellenbeschriftung aus
	echo "<div class='row'><div class='inline' id='active'><b>Aktiv</b>
	</div><div class='inline' id='email'><b>Email</b>
	</div><div class='inline' id='level'><b>Betreuer?</b>
	</div><div class='inline' id='user_action'><b>Druckrechte</b>
	</div></div>";
    if ($stmt = $mysqli->query("SELECT id, email, level, active, rights FROM users")) {
        // hole Variablen von result.
        while ($row = $stmt->fetch_row()){
			$level_img = "<img src='images/rang_hoch.png' alt='Nein'>";
			$rights_img = "<img src='images/löschen.png' alt='Nein'>";
			$active_img = "<img src='images/inaktiv.png' alt='Inaktiv'>";
			if ($row[2] > 0) { 
				$level_img = "<img src='images/erfolg.png' alt='Ja'>";
			}
			if ($row[3] > 0) {
				$active_img = "<img src='images/erfolg.png' alt='Aktiv'>";
			}
			if ($row[4] > 0) { 
				$rights_img = "<img src='images/erfolg.png' alt='Ja'>";
			}
			// gebe Benutzer zeilenweiße aus 
			echo "<div class='row'><div class='inline' id='active'><label for='active" . $row[0] . "'>" . $active_img . "</label>
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

function show_prints($mysqli) {		// Zeige alle Druckaufträge aus Warteschlange an
	// gibt die Tabellenbeschriftung aus
	echo "<div class='row'><div class='inline' id='user'><b>Benutzer</b>
	</div><div class='inline' id='file_name'><b>Datei</b>
	</div><div class='inline' id='priority'><b>Priorität</b>
	</div><div class='inline' id='print_time'><b>Dauer</b>
	</div><div class='inline' id='delete'>
	</div></div>";
    if ($stmt = $mysqli->query("SELECT users.email, to_print.id, to_print.file_name, to_print.priority, to_print.print_time, to_print.time FROM to_print INNER JOIN users on to_print.user_id=users.id ORDER BY to_print.priority DESC, time ASC")) {
        // hole Variablen von result.
        while ($row = $stmt->fetch_row()){
			$delete_img = "<img title='Löschen' src='images/löschen.png' alt='Nein'>";
			$priority_img = "<img title='Priorität' src='images/stern.png' alt='+'>";
			// gebe Aufträge zeilenweiße aus 
			echo "<div class='row'><div class='inline' id='user'>" . substr($row[0], 0, strpos($row[0], "@")) . 		// Gebe Benutzer ohne Email-Endung an
			     "</div><div class='inline' id='file_name'>" . substr($row[2], 0, strpos($row[2], ".gcode")) . 			// Gebe Dateiname ohne gcode-Endung an
				 "</div><div class='inline' id='priority'><label>" . str_repeat($priority_img, $row[3]) .
				 "</label></div><div class='inline' id='print_time'>" . seconds_to_time($row[4]) .
				 "</div><div class='inline right'>
				  <label for='deleteprint" . $row[1] . "'>". $delete_img . "</label>
				  <input class='user_button' type='submit' name='deleteprint' id='deleteprint" . $row[1] . "' value='" .  $row[1] . "'></div></div>";
		}
		// Free result 
		$stmt->close();
	}
}

//
//	Ändere Datenbank

function change_rights($mysqli, $user_id) {		// Gebe ausgewähltem Benutzer Druckrechte
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
}
function change_user_level($mysqli, $user_id) {	// Ändere das Level des ausgewählten Benutzers
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
				if (! $stmt->execute()) {
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
				if (! $stmt->execute()) {
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


function insert_print($mysqli, $user_id, $file_name, $priority, $print_time) {		// Trage Druckauftrag in Warteschlange ein
	$now = time();
	if ($insert_stmt = $mysqli->prepare("INSERT INTO to_print (user_id, file_name, priority, print_time, time) VALUES (?, ?, ?, ?, ?)")) {
        $insert_stmt->bind_param('isiss', $user_id, $file_name, $priority, $print_time, $now);
        // Führe die vorbereitete Anfrage aus.
        if (! $insert_stmt->execute()) {
            header('Location: ../error.php?err=Druck Fehler: INSERT Druck');
        }
		db_history($mysqli, "inserted print " . $file_name . " with priority " . $priority, $user_id);			// trage in DB History Tabelle ein
    }
}
function delete_print($mysqli, $print_id) {		// Lösche ausgewählten Auftrag
	if ($stmt = $mysqli->prepare("DELETE FROM to_print WHERE id = ?")) {
        $stmt->bind_param('i', $print_id);
		// Führe die vorbereitet Abfrage aus.
		if (! $stmt->execute()) {
            header('Location: ../error.php?err=Druck Fehler: DELETE');
        }	
		db_history($mysqli, "deleted print", $print_id);			// trage in DB History Tabelle ein
		echo "<div id='response'>Druck Nr. " . $_POST['deleteprint'] . " gelöscht.</div>";
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
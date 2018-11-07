<?php

function show_users($mysqli) {
	// gibt die Tabellenbeschriftung aus
	echo "<div class='row'><div class='inline' id='id'> ID 
	</div><div class='inline' id='email'> Email 
	</div><div class='inline' id='level'> Betreuer?
	</div><div class='inline' id='delete'> Lösche Benutzer
	</div><div class='inline' id='level'> Ändere Rechte
	</div></div>";
    if ($stmt = $mysqli->query("SELECT id, email, level FROM users")) {
        // hole Variablen von result.
        while ($row = $stmt->fetch_row()){
			$admin = "-";
			if ($row[2] > 0) { $admin = "Ja"; }
			// gebe Benutzer zeilenweiße aus 
			echo "<div class='row'><div class='inline' id='id'>" . $row[0] . 
			     "</div><div class='inline' id='email'>" . $row[1] . 
				 "</div><div class='inline' id='level'>" . $admin . 
				 "</div><input class='delete' type='radio' name='deleteuser' id='delete" . $row[0] . "' value='" .  $row[0] . "'  onclick='javascript:uncheck(delete" . $row[0] . ")'>
						<input class='level' type='radio' name='level' id='level" . $row[0] . "' value='" .  $row[0] . "'  onclick='javascript:uncheck(level" . $row[0] . ")'></div>";
		}
		// Free result 
		$stmt->close();
	}
}
function delete_user($mysqli, $user_id) {		// Lösche ausgewählten Benutzer
	if ($stmt = $mysqli->prepare("DELETE FROM users WHERE id = ?")) {
        $stmt->bind_param('i', $user_id);
		// Führe die vorbereitet Abfrage aus.
		if (! $stmt->execute()) {
            header('Location: ../error.php?err=Verwaltungs Fehler: DELETE');
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
					header('Location: ../error.php?err=Verwaltungs Fehler: UPDATE');
				}
			}
		}
	}
}
function log_history($mysqli, $user_id, $request, $path, $data="NULL") {
	$now = time();
	// Logge alle HTTP post & put Anfragen
	if ($insert_stmt = $mysqli->prepare("INSERT INTO history (user_id, request, time, path, data) VALUES (?, ?, ?, ?, ?)")) {
        $insert_stmt->bind_param('issss', $user_id, $request, $now, $path, $data);
        // Führe die vorbereitete Anfrage aus.
        if (! $insert_stmt->execute()) {
            header('Location: ../error.php?err=Logging Fehler: INSERT');
        }
    }
}
function insert_print($mysqli, $user_id, $file_name, $priority, $print_time) {		// Trage Druckauftrag in Warteschlange ein
	$now = time();
	if ($insert_stmt = $mysqli->prepare("INSERT INTO to_print (user_id, file_name, priority, print_time, time) VALUES (?, ?, ?, ?, ?)")) {
        $insert_stmt->bind_param('isiss', $user_id, $file_name, $priority, $print_time, $now);
        // Führe die vorbereitete Anfrage aus.
        if (! $insert_stmt->execute()) {
            header('Location: ../error.php?err=Druck Fehler: INSERT');
        }
    }
}
function show_prints($mysqli) {		// Zeige alle Druckaufträge aus Warteschlange an
	// gibt die Tabellenbeschriftung aus
	echo "<div class='row'><div class='inline' id='user'> Benutzer 
	</div><div class='inline' id='file_name'> Datei
	</div><div class='inline' id='priority'> Priorität
	</div><div class='inline' id='print_time'>Dauer
	</div><div class='inline' id='delete'> Lösche Auftrag
	</div></div>";
    if ($stmt = $mysqli->query("SELECT users.email, to_print.id, to_print.file_name, to_print.priority, to_print.print_time, to_print.time FROM to_print INNER JOIN users on to_print.user_id=users.id ORDER BY to_print.priority DESC, time ASC")) {
        // hole Variablen von result.
        while ($row = $stmt->fetch_row()){
			// gebe Aufträge zeilenweiße aus 
			echo "<div class='row'><div class='inline' id='user'>" . substr($row[0], 0, strpos($row[0], "@")) . 		// Gebe Benutzer ohne Email-Endung an
			     "</div><div class='inline' id='file_name'>" . substr($row[2], 0, strpos($row[2], ".gcode")) . 			// Gebe Dateiname ohne gcode-Endung an
				 "</div><div class='inline' id='priority'>" . str_repeat("+", $row[3]) .
				 "</div><div class='inline' id='print_time'>" . seconds_to_time($row[4]) .
						"</div><input class='delete' type='radio' name='deleteprint' id='deleteprint" . $row[1] . "' value='" .  $row[1] . "'  onclick='javascript:uncheck(deleteprint" . $row[1] . ")'></div>";
		}
		// Free result 
		$stmt->close();
	}
}
function delete_print($mysqli, $print_id) {		// Lösche ausgewählten Auftrag
	if ($stmt = $mysqli->prepare("DELETE FROM to_print WHERE id = ?")) {
        $stmt->bind_param('i', $print_id);
		// Führe die vorbereitet Abfrage aus.
		if (! $stmt->execute()) {
            header('Location: ../error.php?err=Druck Fehler: DELETE');
        }
    }
}

//
//	Verarbeite POST Requests

if (isset($_POST['deleteuser']) and isset($_POST['level']) and $_POST['deleteuser'] == $_POST['level']) {
	echo "<div id='response'>Bitte nicht sowohl 'Löschen' als auch 'Rechte ändern' für den gleichen Benutzer ankreuzen!</div>";
} else if (isset($_POST['deleteuser']) or isset($_POST['level'])) {
	$delete_user = $_POST['deleteuser'];
	$level_user = $_POST['level'];
	if (isset($delete_user)) {
		echo "<div id='response'>Benutzer Nr. " . $delete_user . " gelöscht.</div>";
		delete_user($mysqli, $delete_user);
	} 
	if (isset($level_user)) {
		echo "<div id='response'>Rechte des Benutzers Nr. " . $level_user . " geändert.</div>";
		change_user_level($mysqli, $level_user);
	}
}
if (isset($_POST['deleteprint'])) {
	$delete_print = $_POST['deleteprint'];
	echo "<div id='response'>Druck Nr. " . $delete_print . " gelöscht.</div>";
	delete_print($mysqli, $delete_print);
}
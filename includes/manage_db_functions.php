<?php
/**
* Funktionen zur Verwaltung und Ausgabe der Datenbank
*
* @author   Tom Lehmann
* @version  1.0
* 
*/

//
//	Funktionen zur Datenbankdarstellung
//

/**
* Gibt die Liste mit den registrierten Benutzern aus
*
* @param mysqli		$mysqli 	Die zu verwendende Datenbankverbindung für die MYSQL Abfragen
*
*/
function show_users($mysqli) {
	// gibt die Tabellenbeschriftung aus
	echo "<div class='row'><div class='inline' id='id'><b>#</b>
	</div><div class='inline' id='active'><b>Aktiv</b>
	</div><div class='inline' id='email'><b>Email</b>
	</div><div class='inline' id='level'><b>Betreuer?</b>
	</div><div class='inline' id='user_action'><b>Druckrechte</b>
	</div></div>";
    if ($stmt = $mysqli->query("SELECT id, email, level, active, rights FROM users ORDER BY id DESC")) {
        
        while ($row = $stmt->fetch_row()){
			$level_img = "<img src='images/rang_hoch.png' alt='Nein' title='Zum Betreuer befördern'>";		// Bild wechles angezeigt wird um Benutzer zum Betreuer zu befördern
			$rights_img = "<img src='images/löschen.png' alt='Nein' title='Druck Rechte erteilen'>";		// Bild wechles angezeigt wird um Benutzer Druck-Rechte zu erteilen
			$active_img = "<img src='images/inaktiv.png' alt='Inaktiv' title='Account akvitieren'>";		// Bild welches angezeigt wird um Benutzer-Account zu aktievieren
			if ($row[2] > 0) { 
				$level_img = "<img src='images/erfolg.png' alt='Ja' title='Betreuer Rechte entziehen'>";	// Bild welches angezeigt wird falls Benutzer schon Betreuer ist
			}
			if ($row[3] > 0) {
				$active_img = "<img src='images/erfolg.png' alt='Aktiv' title='Account deakvitieren'>";		// Bild welches angezeigt wird falls Benutzer-Account schon aktiviert ist
			}
			if ($row[4] > 0) { 
				$rights_img = "<img src='images/erfolg.png' alt='Ja' title= 'Druck Rechte entziehen'>";		// Bild welches angezeigt wird falls Benutzer Druckrechte schon hat
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
		$stmt->close();
	}
}

/**
* Gibt die Liste mit der verfügbaren Druckdateien aus
*
* @param mysqli		$mysqli 	Die zu verwendende Datenbankverbindung für die MYSQL Abfragen
*
*/
function show_files($mysqli) {		
	// gibt die Tabellenbeschriftung aus
	echo "<div class='row'><div class='inline' id='user'><b>Von</b>
	</div><div class='inline' id='file_name'><b>Datei</b>
	</div><div class='inline' id='date'><b>Erstelldatum</b>
	</div><div class='inline' id='print_time'><b>Dauer</b>
	</div><div class='inline' id='print'>
	</div></div>";
	$print_img = "<img title='Druckauftrag einreihen' src='images/print.png' alt='Drucken'>";		// Bild das angezeigt wird um den Druckauftrag zu erteilen
    if ($stmt = $mysqli->query("SELECT users.email, available_prints.id, available_prints.file_name, available_prints.print_time, available_prints.time, available_prints.user_id 
								FROM available_prints 
								INNER JOIN users 
								ON available_prints.user_id=users.id 
								ORDER BY time DESC")) {
        while ($row = $stmt->fetch_row()){
			// gebe Aufträge zeilenweiße aus 
			echo "<div class='row'><div class='inline' id='user'>" . substr($row[0], 0, strpos($row[0], "@")) . 		// Gebe Benutzer ohne Email-Endung an
			     "</div><div class='inline' id='file_name'>" . substr($row[2], 0, strpos($row[2], ".gcode")) . 			// Gebe Dateiname ohne gcode-Endung an
				 "</div><div class='inline' id='date'>" . date("H:i \a\m d.m.Y", $row[4]) .								// Gebe Erstelldatum an
				 "</div><div class='inline' id='print_time'>" . seconds_to_time($row[3]) .								// Gebe Druckdauer an
				 "</div>";
			if (printrights_check($mysqli)) {																			// Wenn Druckrechte vorhanden, gib Möglichkeit Druckauftrag zu erteilen
				echo "<div class='inline right'>
				  <label for='printfile" . $row[1] . "'>". $print_img . "</label>
				  <input class='user_button' type='submit' name='printfile' id='printfile" . $row[1] . "' value='" .  $row[1] . "'></div>";
			}
			echo "</div>";
		}	
		$stmt->close();
	}
}

/**
* Gibt die Druckwarteschlange aus
*
* @param mysqli		$mysqli 	Die zu verwendende Datenbankverbindung für die MYSQL Abfragen
*
*/
function show_prints($mysqli) {		
	// gibt die Tabellenbeschriftung aus
	echo "<div class='row'><div class='inline' id='id'><b>#</b>
	</div><div class='inline' id='user'><b>Benutzer</b>
	</div><div class='inline' id='file_name'><b>Datei</b>
	</div><div class='inline' id='priority'><b>Priorität</b>
	</div><div class='inline' id='print_time'><b>Dauer</b>
	</div><div class='inline' id='delete'>
	</div></div>";
	$delete_img = "<img title='Löschen' src='images/löschen.png' alt='x'>";					// Bild das angezeigt wird um Druck zu löschen
	$priority_img = "<img title='Priorität' src='images/stern2.png' alt='*'>";				// Bild das angezeigt wird um Priorität anzuzeigen
	$priority_minus_img = "<img title='Priorität senken' src='images/minus.png' alt='-'>";	// Bild das angezeigt wird um Priorität zu senken
	$priority_plus_img = "<img title='Priorität erhöhen' src='images/plus.png' alt='+'>";	// Bild das angezeigt wird um Priorität zu erhöhen
    if ($stmt = $mysqli->query("SELECT users.email, to_print.file_id, available_prints.id, available_prints.file_name, to_print.priority, available_prints.print_time, to_print.time, to_print.user_id, to_print.id 
								FROM to_print 
								INNER JOIN users ON to_print.user_id=users.id 
								INNER JOIN available_prints on available_prints.id=to_print.file_id
								ORDER BY to_print.priority DESC, cast(available_prints.print_time AS unsigned) ASC")) {
        while ($row = $stmt->fetch_row()){
			if ($row[4] < 1) $row[4] = 0;
			// gebe Aufträge zeilenweiße aus 
			echo "<div class='row'><div class='inline' id='id'>" . $row[8] . 
				 "</div><div class='inline' id='user'>" . substr($row[0], 0, strpos($row[0], "@")) . 					// Gebe Benutzer ohne Email-Endung an
			     "</div><div class='inline' id='file_name'>" . substr($row[3], 0, strpos($row[3], ".gcode")) . 			// Gebe Dateiname ohne gcode-Endung an
				 "</div><div class='inline' id='priority'>";
			if (admin_check($mysqli) > 0) {																				// Wenn der Benutzer Betreuer ist, gib Möglichkeit die Priorität zu ändern
				if ($row[4] > 0) {																						// Wenn Priorität größer 0, gib Möglichkeit Priorität zu senken
					echo "<label for='prio_minus" . $row[8] . "'>". $priority_minus_img . "</label>" .
						 "<input class='user_button' type='submit' name='prio_minus' id='prio_minus" . $row[8] . "' value='" .  $row[8] . "'>";
				}
			}
			echo "<label>" . str_repeat($priority_img, $row[4]) . "</label>";
			if (admin_check($mysqli) > 0) {																				// Wenn der Benutzer Betreuer ist, gib Möglichkeit die Priorität zu ändern
				if ($row[4] < 6) {																						// Wenn Priorität kleiner als das Maximum ist, gib Möglichkeit Priorität zu erhöhen
					echo "<label for='prio_plus" . $row[8] . "'>". $priority_plus_img . "</label>
						  <input class='user_button' type='submit' name='prio_plus' id='prio_plus" . $row[8] . "' value='" .  $row[8] . "'>";
				}
			}
			echo "</div><div class='inline' id='print_time'>" . seconds_to_time($row[5]) .								// Gib Druckdauer aus
				 "</div>";
			if (login_check($mysqli) == $row[7] or admin_check($mysqli) > 0) {											// Falls Benutzer Besitzer des Drucks oder Betreuer ist, gib Möglichkeit Druck zu löschen
				echo "<div class='inline right'>
				  <label for='deleteprint" . $row[8] . "'>". $delete_img . "</label>
				  <input class='user_button' type='submit' name='deleteprint' id='deleteprint" . $row[8] . "' value='" .  $row[8] . "'></div>";
			}
			echo "</div>";
		}
		$stmt->close();
	}
}

//
//	Funktionen zur Änderung der Datenbank
//

/**
* Funktion zum ändern der Druckrechte
*
* @param mysqli		$mysqli 	Die zu verwendende Datenbankverbindung für die MYSQL Abfragen
* @param int		$user_id	Die eindeutige Benutzer-ID des Benutzers bei dem die Druckrechte geändert werden sollen
*
*/
function change_rights($mysqli, $user_id) {		
	if ($stmt = $mysqli->prepare("SELECT level FROM users WHERE id = ? LIMIT 1")) {
        $stmt->bind_param('i', $user_id);
		$stmt->execute();   
        $stmt->store_result();
		
		if ($stmt->num_rows == 1) {
			$stmt->bind_result($user_level);
			$stmt->fetch();
			if ($user_level < 1) {																		// Wenn Benutzer kein Betreuer ist, ändere Druckrechte
				if ($stmt = $mysqli->prepare("SELECT rights FROM users WHERE id = ? LIMIT 1")) {
					$stmt->bind_param('i', $user_id);
					$stmt->execute();   
					$stmt->store_result();
					if ($stmt->num_rows == 1) {
						$stmt->bind_result($old_user_rights);
						$stmt->fetch(); 
						$new_user_rights = 0;															// Wenn Benutzer Druckrechte hatte dann setze Rechte 0
						if ($old_user_rights == 0) { $new_user_rights = 1; } 							// Ansonsten setze Rechte auf 1
						if ($stmt = $mysqli->prepare("UPDATE users SET rights = ? WHERE id = ?")) {
							$stmt->bind_param('ii', $new_user_rights, $user_id);
							
							if (!$stmt->execute()) {
								header('Location: ../error.php?err=Verwaltungs Fehler: UPDATE Rechte');	// Falls dabei ein Fehler passiert, gebe dies aus
							}
						db_history($mysqli, "changed user rights", $user_id);							// trage in DB History Tabelle ein
						}
					}
					if ($new_user_rights == 1) {														// Gebe Status der Änderung aus
						echo "<div id='response'>Dem Benutzer Nr. " . $user_id . " Druckrechte erteilt.</div>";
					} else {
						echo "<div id='response'>Dem Benutzer Nr. " . $user_id . " Druckrechte entzogen.</div>";
					}
				}
			} else echo "<div id='response'>Dem Benutzer Nr. " . $user_id . " können keine Druckrechte entzogen werden, da er ein Betreuer ist.</div>";
		}
		$stmt->close();
	}
}

/**
* Funktion zum ändern der Betreuerrechte
*
* @param mysqli		$mysqli 	Die zu verwendende Datenbankverbindung für die MYSQL Abfragen
* @param int		$user_id	Die eindeutige Benutzer-ID des Benutzers bei dem die Betreuerrechte geändert werden sollen
*
*/
function change_user_level($mysqli, $user_id) {	
	if ($stmt = $mysqli->prepare("SELECT level FROM users WHERE id = ? LIMIT 1")) {
        $stmt->bind_param('i', $user_id);
		$stmt->execute();   
        $stmt->store_result();
		
		if ($stmt->num_rows == 1) {
			$stmt->bind_result($old_user_level);
			$stmt->fetch();
		
			$new_user_level = 0;														// Wenn Benutzer Betreuerrechte hatte dann setze Level 0
			if ($old_user_level == 0) { $new_user_level = 1; } 							// Ansonsten setze Level auf 1
			if ($stmt = $mysqli->prepare("UPDATE users SET level = ? WHERE id = ?")) {
				$stmt->bind_param('ii', $new_user_level, $user_id);
				
				if (!$stmt->execute()) {
					header('Location: ../error.php?err=Verwaltungs Fehler: UPDATE Level');
				}
			db_history($mysqli, "changed user level", $user_id);						// trage in DB History Tabelle ein
			}
		}
		if ($new_user_level == 1) {
			echo "<div id='response'>Dem Benutzer Nr. " . $user_id . " Betreuerrechte erteilt.</div>";
		} else {
			echo "<div id='response'>Dem Benutzer Nr. " . $user_id . " Betreuerrechte entzogen.</div>";
		}
		$stmt->close();
	}
}

/**
* Funktion zum ändern ob ein Account aktiviert ist
*
* @param mysqli		$mysqli 	Die zu verwendende Datenbankverbindung für die MYSQL Abfragen
* @param int		$user_id	Die eindeutige Benutzer-ID des Benutzers bei dem der Status der Aktivität geändert werden soll
*
*/
function change_user_active($mysqli, $user_id) {	
	if ($stmt = $mysqli->prepare("SELECT active FROM users WHERE id = ? LIMIT 1")) {
        $stmt->bind_param('i', $user_id);
		$stmt->execute();   
        $stmt->store_result();
		
		if ($stmt->num_rows == 1) {
			$stmt->bind_result($old_user_active);
			$stmt->fetch();
		
			$new_user_active = 0;															// Wenn Benutzer aktiv war dann setze auf inaktiv
			if ($old_user_active == 0) { $new_user_active = 1; } 							// Ansonsten setze auf aktiv
			if ($stmt = $mysqli->prepare("UPDATE users SET active = ? WHERE id = ?")) {
				$stmt->bind_param('ii', $new_user_active, $user_id);
				
				if (!$stmt->execute()) {
					header('Location: ../error.php?err=Verwaltungs Fehler: UPDATE Aktiv');
				}
			db_history($mysqli, "changed user active", $user_id);							// trage in DB History Tabelle ein
			}
		}
		if ($new_user_active == 1) {
			echo "<div id='response'>Account des Benutzer Nr. " . $user_id . " aktiviert.</div>";
		} else {
			echo "<div id='response'>Account des Benutzer Nr. " . $user_id . " deaktiviert.</div>";
		}
		$stmt->close();
	}
}


//
//	Funktionen zur Druckauftragsverwaltung
//

/**
* Funktion zum Einfügen einer Datei zu den "druckbaren" Dateien
*
* @param mysqli		$mysqli 	Die zu verwendende Datenbankverbindung für die MYSQL Abfragen
* @param int		$user_id	Die eindeutige Benutzer-ID des Besitzers
* @param string		$file_name	Der Dateiname der Datei
* @param int		$print_time	Die Druckdauer der Datei
*
*/
function insert_file($mysqli, $user_id, $file_name, $print_time) {		
	$now = time();   																		// Erstelldatum der Datei
	if ($stmt = $mysqli->prepare("SELECT id FROM available_prints WHERE file_name = ?")) {			// Hole alte ID des 3D-Modells
        $stmt->bind_param('s', $file_name);
		$stmt->execute();   
        $stmt->store_result();
		if ($stmt->num_rows == 1) {
			$stmt->bind_result($old_id);
			$stmt->fetch();
			$stmt->close();															
			$stmt = $mysqli->prepare("DELETE FROM available_prints WHERE file_name = ?"); 			// Lösche den alten Eintrag aus der Tabelle falls bereits vorhanden, da die Datei ersetzt wird
			$stmt->bind_param('s', $file_name);
			$stmt->execute();
			$stmt->close();	
			if ($insert_stmt = $mysqli->prepare("INSERT INTO available_prints (id, user_id, file_name, print_time, time) VALUES (?, ?, ?, ?, ?)")) {
				$insert_stmt->bind_param('iisss', $old_id, $user_id, $file_name, $print_time, $now);
			   
				if (! $insert_stmt->execute()) {
					header('Location: ../error.php?err=Druck Fehler: INSERT Datei');
				}
				db_history($mysqli, "inserted file " . $file_name, $user_id);						// trage in DB History Tabelle ein
				echo "<div id='response'>Datei ". $file_name . " hochgeladen.</div>";
				$stmt->close();
			}
		} else {
			if ($insert_stmt = $mysqli->prepare("INSERT INTO available_prints (user_id, file_name, print_time, time) VALUES (?, ?, ?, ?)")) {
				$insert_stmt->bind_param('isss', $user_id, $file_name, $print_time, $now);
			   
				if (! $insert_stmt->execute()) {
					header('Location: ../error.php?err=Druck Fehler: INSERT Datei');
				}
				db_history($mysqli, "inserted file " . $file_name, $user_id);						// trage in DB History Tabelle ein
				echo "<div id='response'>Datei ". $file_name . " hochgeladen.</div>";
				$stmt->close();
			}
		}
	}
}

/**
* Funktion zum Einfügen eines Druckauftrags in die Warteschlange
*
* @param mysqli		$mysqli 	Die zu verwendende Datenbankverbindung für die MYSQL Abfragen
* @param int		$user_id	Die eindeutige Benutzer-ID des Druckauftraggebenden
* @param int		$file_id	Die eindeutige Datei-ID 
* @param int		$priority	Die Priorität des Drucks
*
*/
function insert_print($mysqli, $user_id, $file_id, $priority) {		
	$now = time();
	if ($insert_stmt = $mysqli->prepare("INSERT INTO to_print (user_id, file_id, priority, time) VALUES (?, ?, ?, ?)")) {
        $insert_stmt->bind_param('iiis', $user_id, $file_id, $priority, $now);
        
        if (! $insert_stmt->execute()) {
            header('Location: ../error.php?err=Druck Fehler: INSERT Druck');
        }
		db_history($mysqli, "inserted print " . $file_id . " with priority " . $priority, $user_id);
		echo "<div id='response'>Druck erfolgreich in die Warteschlange aufgenommen!</div>";
		$insert_stmt->close();
    }
}

/**
* Funktion zum Löschen eines Druckauftrags in die Warteschlange
*
* @param mysqli		$mysqli 	Die zu verwendende Datenbankverbindung für die MYSQL Abfragen
* @param int		$print_id	Die eindeutige Druck-ID des Druckauftrags
*
*/
function delete_print($mysqli, $print_id) {		
	if ($stmt = $mysqli->prepare("DELETE FROM to_print WHERE id = ?")) {
        $stmt->bind_param('i', $print_id);
		
		if (! $stmt->execute()) {
            header('Location: ../error.php?err=Druck Fehler: DELETE Druck');
        }	
		db_history($mysqli, "deleted print", $print_id);
		echo "<div id='response'>Druck Nr. " . $_POST['deleteprint'] . " gelöscht.</div>";
		$stmt->close();
    }
}

/**
* Funktion zum Erhöhen der Priorität eines Druckauftrags
*
* @param mysqli		$mysqli 	Die zu verwendende Datenbankverbindung für die MYSQL Abfragen
* @param int		$print_id	Die eindeutige Druck-ID des Druckauftrags
*
*/
function prio_plus($mysqli, $print_id) {		
	if ($stmt = $mysqli->prepare("SELECT priority FROM to_print WHERE id = ?")) {		
        $stmt->bind_param('i', $print_id);
		$stmt->execute();   
        $stmt->store_result();
		
		if ($stmt->num_rows == 1) {
			$stmt->bind_result($priority);
			$stmt->fetch();
			if ($priority < 6) {														// Prüfe ob Priorität nicht schon maximal ist
				if ($stmt = $mysqli->prepare("UPDATE to_print SET priority = priority + 1 WHERE id = ?")) {
					$stmt->bind_param('i', $print_id);
					if (! $stmt->execute()) {
						header('Location: ../error.php?err=Druck Fehler: Update Priorität plus');
					}	
					db_history($mysqli, "priority plus", $print_id);			
					echo "<div id='response'>Priorität erfoglreich erhöht.</div>";
				}
			} else echo "<div id='response'>Die Priorität ist schon die Höchste.</div>";
		}
		$stmt->close();
	}
}

/**
* Funktion zum Senken der Priorität eines Druckauftrags
*
* @param mysqli		$mysqli 	Die zu verwendende Datenbankverbindung für die MYSQL Abfragen
* @param int		$print_id	Die eindeutige Druck-ID des Druckauftrags
*
*/
function prio_minus($mysqli, $print_id) {		
	if ($stmt = $mysqli->prepare("SELECT priority FROM to_print WHERE id = ?")) {		
        $stmt->bind_param('i', $print_id);
		$stmt->execute();   // Execute the prepared query.
        $stmt->store_result();
		
		if ($stmt->num_rows == 1) {
			$stmt->bind_result($priority);
			$stmt->fetch();
			if ($priority > 0) {												// Prüfe ob Priorität nicht schon Null ist
				if ($stmt = $mysqli->prepare("UPDATE to_print SET priority = priority - 1 WHERE id = ?")) {
					$stmt->bind_param('i', $print_id);
					if (! $stmt->execute()) {
						header('Location: ../error.php?err=Druck Fehler: Update Priorität minus');
					}	
					db_history($mysqli, "priority minus", $print_id);
					echo "<div id='response'>Priorität erfoglreich gesenkt.</div>";
				}
			} else echo "<div id='response'>Die Priorität ist schon die Niedrigste.</div>";
		}
		$stmt->close();
	}
}

//
// Funktionen zur Verwaltung der Emails
//

/**
* Funktion zum Speichern der zu sendenden Emails in der Datenbank
*
* @param mysqli		$mysqli 	Die zu verwendende Datenbankverbindung für die MYSQL Abfragen
* @param int		$print_id	Die eindeutige Druck-ID des Druckauftrags
* @param string		$at			UNIX-Timestamp des Sendedatums
* @param string		$to			Empfängeradresse
* @param string		$file		Dateinamen des Druckauftrags
* @param string		$event		Der Auslöser (gestartet, gestoppt) für die Benachrichtigung
*
*/
function new_mail($mysqli, $print_id, $at, $to, $file, $event) {
	if ($insert_stmt = $mysqli->prepare("INSERT INTO mails (`print_id`, `at`, `to`, `file`, `event`) VALUES (?, ?, ?, ?, ?)")) {
		$insert_stmt->bind_param('issss', $print_id, $at, $to, $file, $event);
		
		if (!$insert_stmt->execute()) {
			header('Location: ../error.php?err=Druck Fehler: INSERT mails');
		}
		$insert_stmt->close();
	}
}

/**
* Funktion zum Löschen der zu gespeicherten Emails in der Datenbank
*
* @param mysqli		$mysqli 	Die zu verwendende Datenbankverbindung für die MYSQL Abfragen
* @param int		$print_id	Die eindeutige Druck-ID des Druckauftrags
*
*/
function delete_mail($mysqli, $print_id) {
	if ($insert_stmt = $mysqli->prepare("DELETE FROM mails WHERE print_id = ?")) {			// Löscht alle Emails zum zugehörigen Druckauftrag
		$insert_stmt->bind_param('i', $print_id);
		
		if (!$insert_stmt->execute()) {
			header('Location: ../error.php?err=Druck Fehler: DELETE mails');
		}
		$insert_stmt->close();
	}
}


//
//	Log Funktionen
//

/**
* Funktion zum Loggen aller HTTP post & put Anfragen
*
* @param mysqli		$mysqli 	Die zu verwendende Datenbankverbindung für die MYSQL Abfragen
* @param int		$user_id	Die eindeutige Benutzer-ID 
* @param string		$request	Die HTTP-Anfrage Art
* @param string		$path		Der API-Endpunkt
* @param json		$data		Die mitgesendeten Daten
*
*/
function printer_history($mysqli, $user_id, $request, $path, $data="NULL") {
	$now = time();
	if ($insert_stmt = $mysqli->prepare("INSERT INTO printer_history (user_id, request, time, path, data) VALUES (?, ?, ?, ?, ?)")) {
        $insert_stmt->bind_param('issss', $user_id, $request, $now, $path, $data);

        if (!$insert_stmt->execute()) {
            header('Location: ../error.php?err=Logging Fehler: INSERT Printer Log');
        }
		$insert_stmt->close();
    }
}

/**
* Funktion zum Loggen aller sonstiger Benutzerinteraktionen
*
* @param mysqli		$mysqli 	Die zu verwendende Datenbankverbindung für die MYSQL Abfragen
* @param string		$action		Die Benutzerinteraktion
* @param json		$data		Die mitgesendeten Daten
*
*/
function db_history($mysqli, $action, $data) {
	$now = time();
	if ($insert_stmt = $mysqli->prepare("INSERT INTO db_history (action, time, data) VALUES (?, ?, ?)")) {
        $insert_stmt->bind_param('sss', $action, $now, $data);

        if (!$insert_stmt->execute()) {
            header('Location: ../error.php?err=Logging Fehler: INSERT DB Log');
        }
		$insert_stmt->close();
    }
}

//
// Die Abfragen, ob eine POST-Anfrage vorliegt um die Datenbank zu ändern (ob ein Benutzer einen Button betätigt hat)
//

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
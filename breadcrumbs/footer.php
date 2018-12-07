<?php
if ($stmt = $mysqli->query("SELECT users.email, to_print.file_id, available_prints.id, available_prints.file_name, to_print.priority, available_prints.print_time, to_print.time, to_print.user_id 
								FROM to_print 
								INNER JOIN users ON to_print.user_id=users.id 
								INNER JOIN available_prints on available_prints.id=to_print.file_id
								ORDER BY to_print.priority DESC, time ASC")) {
	$next_print = $stmt->fetch_row();
} else echo "<div id='response'><b>Fehler</b>: Nächster Druck konnte nicht ermittelt werden!</div>";
if (get_status($ulti) == "im Leerlauf" && empty(format_state(get_state($ulti)))) $state = "idle"; 	// Wenn der Drucker am Drucken ist wird der Start-Knopf deaktiviert, sonst der Stopp-Knopf deaktiviert
else $state = "busy";
?>

<footer>
	<form action="<?php echo esc_url($_SERVER['PHP_SELF']); ?>" method="post" name="printer_button" id="printer_buttons">
		<div class=''>
			<span id='next_print'>
					Nächster Druck: <?php echo substr(substr($next_print[0], 0, strpos($next_print[0], "@")), 0, 54) . ": " .	// Auftraggeber
					substr($next_print[3], 0, strpos($next_print[3], ".gcode")) . " (" . // Gib den Dateinamen des nächsten Druckes aus (maximal 54 Zeichen) ohne .gcode Endung
					seconds_to_time($next_print[5]) . ")"; // Druckdauer ?> 	
			</span>
			<label for='start_button' 
			<?php
				if ($state == "busy") echo "id='printer_buttons_disabled'>";
				else echo "id='printer_buttons_enabled'>";
			?>
				<img src='images/play.png' alt='Start' class='printer_button_img' title='Starten'>
			</label>
			<?php if ($state == "idle") {
				echo "<input class='printer_button' type='submit' name='start_button' id='start_button' />";
			}
			?>
			<label for='replay_button' 
			<?php
				if ($state == "busy") echo "id='printer_buttons_disabled'>";
				else echo "id='printer_buttons_enabled'>";
			?>
				<img src='images/replay.png' alt='Nochmal' class='printer_button_img' title='Nochmal'>
			</label>
			<?php if ($state == "idle") {
				echo "<input class='printer_button' type='submit' name='replay_button' id='replay_button' />";
			}
			?>
			<label for='stop_button' 
			<?php
				if ($state == "idle") echo "id='printer_buttons_disabled'>";
				else echo "id='printer_buttons_enabled'>";
			?>
				<img src='images/stop.png' alt='Stop' class='printer_button_img printer_button_img_right' title='Stoppen'>
			</label>
			<?php if ($state == "busy") {
				echo "<input class='printer_button' type='submit' name='stop_button' id='stop_button' />";
			}
			?>
		</div>
	</form>
</footer>
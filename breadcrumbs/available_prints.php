<form method="post" action="<?php echo esc_url($_SERVER['PHP_SELF']); ?>" oninput="change_prio();" name="form">		
	<select name="file_to_print" size="6" required="required"> 
	<?php 		// Die Funktion stellt alle hochgeladenen Dateien zur Auswahl
		$dir = "uploads/gcode/";
		foreach (scandir($dir) as $file) {
			if ($file != "." && $file != "..") {	// Ordnerstruktur ausblenden
				$files[$file] = filemtime($dir . '/' . $file);		// Ordne die Dateinamen mit dem zugehörigen Erstellungsdatum in ein Array
			}
		}
		arsort($files);			// Sortiere das Array (in diesem Fall nach dem Datum, neueste zuerst)
		$files = array_keys($files);		// Wieder eindimensionales Array
		
		foreach ($files as $file_name) {
			echo "<option value='" . $file_name . "'>" . $file_name . "</option>";
		}
	?>
	</select>
	
	<p>Priorität: <output name="numerisch">niedrig</output><input type="range" name="priority" min="0" max="<?php 
		if($admin == 'angemeldet') { echo '4'; } else { echo '2'; }
	?>" value="1"></p>
	<p><input type="submit" value="Drucken" /></p>
</form>

<script>
function change_prio() {
	if (form.priority.value == 0) { form.numerisch.value = "keine"; }
	else if (form.priority.value == 1) { form.numerisch.value = "niedrig"; }
	else if (form.priority.value == 2) { form.numerisch.value = "normal"; }
	else if (form.priority.value == 3) { form.numerisch.value = "erhöht"; }
	else if (form.priority.value == 4) { form.numerisch.value = "höchste"; }
}
</script>
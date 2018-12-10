<?php

include_once 'includes/manage_prints_functions.php';


if (get_status($ulti) == "im Leerlauf" && empty(format_state(get_state($ulti)))) $state = "idle"; 	// Wenn der Drucker am Drucken ist wird der Start-Knopf deaktiviert, sonst der Stopp-Knopf deaktiviert
	else $state = "busy";
if (get_next_print($mysqli)) $has_next_print = true; 
	else $has_next_print = false;
?>

<footer>
	<form action="<?php echo esc_url($_SERVER['PHP_SELF']); ?>" method="post" name="printer_button" id="printer_buttons">
		<div class=''>
			<span id='next_print'>
					Nächster Druck: <?php echo show_next_print($mysqli); ?> 	
			</span>
			<label for='start_button' 
			<?php
				if ($state == "idle" and $has_next_print) echo "id='printer_buttons_enabled'>";
				else echo "id='printer_buttons_disabled'>";
			?>
				<img src='images/play2.png' alt='Start' class='printer_button_img' title='Starten'>
			</label>
			<?php if ($state == "idle" and $has_next_print) {
				echo "<input class='printer_button' type='submit' name='start_button' id='start_button' />";
			}
			?>
			<label for='stop_button' 
			<?php
				if ($state == "idle") echo "id='printer_buttons_disabled'>";
				else echo "id='printer_buttons_enabled'>";
			?>
				<img src='images/stop2.png' alt='Stop' class='printer_button_img printer_button_img_right' title='Stoppen'>
			</label>
			<?php if ($state == "busy") {
				echo "<input class='printer_button' type='submit' name='stop_button' id='stop_button' />";
			}
			?>
		</div>
	</form>
</footer>
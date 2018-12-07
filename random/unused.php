<div class='line'> Checke Authentifikation: <?php echo $ulti->get("auth/check/" . $ulti->get_id()); ?> </div>
<div class='line'> Sende Nachricht: <?php //echo $ulti->put("system/display_message", json_encode(array('message' => 'Hallo Welt!', 'button_caption' => ':)'))); ?> </div>

<div class='line'> Ändere LED: <?php echo $ulti->put("printer/led", json_encode(array('brightness' => '100', 'hue' => '224', 'saturation' => '100'))); ?> </div>
	<div class='line'> Ändere Druckerstatus: <?php // echo $ulti->put("print_job/state", json_encode(array('target'=>"abort"))); ?> </div>
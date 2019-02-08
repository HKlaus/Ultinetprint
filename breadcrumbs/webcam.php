	<?php
/**
*
* @author   Tom Lehmann
* @version  1.0
* 
*/

/**
* Wechselt zwischen den zwei verfügbaren Webcams und gibt den Stream aus
* 
*/
echo "<label id='switch'>";
if (get_status($ulti) == 'im Leerlauf') {						// Falls der Drucker gearde im Leerlauf sein sollte, zeige Kamera aus Vogelperspektive
	echo "<img id='webcam_image' src='http://141.28.73.21/webcam/?action=stream' width='640' height='480'>";
} else {														// Ansonsten zeige Drucker-interne Kamera
	echo "<img id='webcam_image' src='" .  str_to_str($ulti->get("camera/feed")) . "' width='640' height='480'>";
}
echo "<input type='checkbox' onclick='switch_cam(this)' id='webcam_switch'></label>";
echo "<script>function switch_cam(cb) {
	if (cb.checked == true) {
		document.getElementById('webcam_image').src='http://141.28.73.21/webcam/?action=stream';
	} else document.getElementById('webcam_image').src='" .  str_to_str($ulti->get("camera/feed")) . "';
}</script>";
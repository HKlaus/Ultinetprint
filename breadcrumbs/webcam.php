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
if (get_status($ulti) == 'im Leerlauf') {						// Falls der Drucker gearde im Leerlauf sein sollte, zeige Kamera aus Vogelperspektive
	echo "<img id='webcam_image' src='http://141.28.73.21/webcam/?action=stream'>";
} else {														// Ansonsten zeige Drucker-interne Kamera
	echo "<img id='webcam_image' src='" .  str_replace('"', '', $ulti->get("camera/feed")) . "'>";
}

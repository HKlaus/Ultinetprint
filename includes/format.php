<?php
/**
*
* @author   Tom Lehmann
* @version  1.0
* 
*/

/**
* Entfernt die Anführungszeichen von einem String einer HTTP-Antwort
*
* @param  string $string 	Der zu formatierende String
*
* @return string
* 
*/
function str_to_str($string) {
	return str_replace('"', "", $string);
}

/**
* Entfernt die Anführungszeichen des "message"-Teils eines Jsons einer HTTP-Antwort
*
* @param  json 	 $message 	Das zu formatierende Json
*
* @return string
* 
*/
function msg_to_str($message) {
	return str_replace('"', "", json_decode($message)->{'message'});
}

/**
* Extrahiert den String des "hotend"->"id"-Teils eines Jsons einer HTTP-Antwort
*
* @param  json 	 $message 	Das zu formatierende Json
*
* @return string
* 
*/
function msg_id_to_str($message) {
	$head1 = json_decode($message)[0]->{'extruders'}[0]->{'hotend'}->{'id'};
	$head2 = json_decode($message)[0]->{'extruders'}[1]->{'hotend'}->{'id'};
	return $head1 . ' | ' . $head2;
}

/**
* Nimmt einen UNIX-Zeitstempel und wandelt ihn in ein leserliches Format um
*
* @param 	int 	$secs	Der UNIX-Zeitstempel
*
* @return 	string		 	Der formatierte Zeitausdruck
*/
function seconds_to_time($secs) {		// Für die Ausgabe der Druckdauer (die sich aus Sekunden errechnet)
	$secs = floatval($secs);
	
	$minutes = floor($secs / 60 % 60);
	$hours = floor($secs / 3600);
	
	if ($minutes < 10) $minutes = "0" . $minutes;
	
	return $hours . ":" . $minutes;
}

/**
* Der Drucker kennt viele verschiedene "states", es muss nicht zwischen allen unterschieden werden und 
* um sie ins Deutsche zu übersetzen und anschaulicher zu machen gibt diese Funktion formatierte Strings zurück
*
* @param  string $state 	Der zu formatierende "state"
*
* @return string
* 
*/
function format_state($state) {
	$attention_img = "<img src='images/achtung.png' id='img' /> ";
	$refresh_img = " <a onClick='window.location.reload()' href=''><img src='images/aktualisieren.png' id='img' /></a>";
	if ($state == "wait_cleanup" or $state == "wait_user_action" or $state == "queued") return $attention_img . "<b>Drucker wartet auf manuelle/lokale Nutzereingabe!</b>" . $refresh_img;
	else if ($state == "pre_print" or $state == "sent_to_printer") return "Druck wird vorbereitet..." . $refresh_img;
	else if ($state == "aborted") return "Druckauftrag abgebrochen" . $refresh_img;
	else if ($state == "pausing" or $state == "paused") return "Druckauftrag pausiert" . $refresh_img;
	else if ($state == "resuming" or $state == "printing") return "Druckauftrag wird ausgeführt" . $refresh_img;
	else if ($state == "{message: Not found}" or $state == "none" or $state == "post_print") return "";			// Falls der Drucker keinen State zurück gibt braucht dieser auch nicht angezeigt werden
	else return $state;
}
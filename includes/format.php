<?php

function str_to_str($string) {
	return str_replace('"', "", $string);
}

function msg_to_str($message) {
	return str_replace('"', "", json_decode($message)->{'message'});
}

function format_state($state) {
	$attention_img = "<img src='images/achtung.png' id='img' /> ";
	$refresh_img = " <a onClick='window.location.reload()' href=''><img src='images/aktualisieren.png' id='img' /></a>";
	if ($state == "wait_cleanup" or $state == "wait_user_action" or $state == "queued") return $attention_img . "<b>Drucker wartet auf manuelle/lokale Nutzereingabe!</b>" . $refresh_img;
	else if ($state == "pre_print" or $state == "sent_to_printer") return "Druck wird vorbereitet..." . $refresh_img;
	else if ($state == "aborted") return "Druckauftrag abgebrochen" . $refresh_img;
	else if ($state == "pausing" or $state == "paused") return "Druckauftrag pausiert" . $refresh_img;
	else if ($state == "resuming" or $state == "printing") return "Druckauftrag wird ausgeführt" . $refresh_img;
	else if ($state == "{message: Not found}" or $state == "none") return "";			// Falls der Drucker keinen State zurück gibt braucht dieser auch nicht angezeigt werden
	else return $state;
}
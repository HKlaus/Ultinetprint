<?php

function str_to_str($string) {
	return str_replace('"', "", $string);
}

function msg_to_str($message) {
	return str_replace('"', "", json_decode($message)->{'message'});
}

function format_state($state) {
	if ($state == "wait_cleanup" or $state == "wait_user_action" or $state == "queued") return "<span style='color:red; font-weight: 600;'>Drucker wartet auf Nutzereingabe!</span>";
	else if ($state == "pre_print" or $state == "sent_to_printer") return "Druck wird vorbereitet...";
	else if ($state == "aborted") return "Druckauftrag abgebrochen";
	else if ($state == "pausing" or $state == "paused") return "Druckauftrag pausiert";
	else if ($state == "resuming" or $state == "printing") return "Druckauftrag wird ausgeführt";
	else if ($state == "{message: Not found}" or $state == "none") return "";			// Falls der Drucker keinen State zurück gibt braucht dieser auch nicht angezeigt werden
	else return $state;
}
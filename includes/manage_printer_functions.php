<?php 
/**
*
* @author   Tom Lehmann
* @version  1.0
* 
*/

/**
* Gibt den Druckernamen zurück
*
* @param Ultimaker3 $ulti 	Der zu verwendende Ultimaker
*
* @return string
´*
*/
function get_name($ulti) {
	return str_to_str($ulti->get("system/name"));
}

/**
* Gibt zurück ob die Website sich erfolgreich beim Drucker verifiziert hat
*
* @param Ultimaker3 $ulti 	Der zu verwendende Ultimaker
*
* @return string
´*
*/
function verify_auth($ulti) {
	$verify = msg_to_str($ulti->get("auth/verify"));
	if ($verify == "ok") return "Erfolgreich beim Drucker verifiziert. <img id='img' src='images/erfolg.png' alt=''>";
	else if ($verify == "Authorization required.") return "Fehler bei der Verifizierung, ID/Key-Paar nicht valide. <img id='img' src='images/löschen.png' alt=''>";
	else return "Fehler bei der Verifizierung, der Drucker ist womöglich ausgeschaltet oder nicht am Netz. <img id='img' src='images/löschen.png' alt=''>";
}

/**
* Gibt den Druckerstatus zurück
*
* @param Ultimaker3 $ulti 	Der zu verwendende Ultimaker
*
* @return string
´*
*/
function get_status($ulti) {
	$status = str_to_str($ulti->get("printer/status"));
	if ($status == "idle") return "im Leerlauf";
	else if ($status == "printing") {
		if (get_state($ulti) == "printing") {
			$print_time = str_to_str($ulti->get("print_job/time_total"));
			$time_elapsed = str_to_str($ulti->get("print_job/time_elapsed"));
			return "<div id='timer'><script> timer('" . date("M j, Y H:i:s", $print_time - $time_elapsed + time()) . "'); </script></div>";
		}  else return "Warte..";
	}
	else if ($status == "maintainance") return "im Wartungsmodus";
	else return $status;
}

/**
* Gibt den aktuellen Druck-Namen zurück
*
* @param Ultimaker3 $ulti 	Der zu verwendende Ultimaker
*
* @return string
´*
*/
function get_printname($ulti) {
	if (str_to_str($ulti->get("printer/status")) == "printing") return str_to_str($ulti->get("print_job/name"));
}

/**
* Gibt den aktuellen Druckfortschritt zurück
*
* @param Ultimaker3 $ulti 	Der zu verwendende Ultimaker
*
* @return string
´*
*/
function get_printprogress($ulti) {
	if (str_to_str($ulti->get("printer/status")) == "printing") return round(str_to_str($ulti->get("print_job/progress")) * 100, 1) . "%";
}

/**
* Gibt den Druck-State zurück
*
* @param Ultimaker3 $ulti 	Der zu verwendende Ultimaker
*
* @return string
´*
*/
function get_state($ulti) {
	return str_to_str($ulti->get("print_job/state"));
}

/**
* Gibt einen String zurück, der beschreibt ob und was der Drucker gerade am drucken ist
*
* @param Ultimaker3 $ulti 	Der zu verwendende Ultimaker
*
* @return string
´*
*/
function is_printing($ulti) {		// Was ist der Drucker gerade am Drucken?
	if (json_decode($ulti->get("print_job/name"))->{'message'} != "Not found") {
		echo "<div id='response'>Die Datei <b>".  $ulti->get("print_job/name") . "</b> wird nun gedruckt.</div>";
		return true;
	} else {
		echo "<div id='response'><b>Fehler</b> beim Drucken.</div>";
		return false;
	}
}
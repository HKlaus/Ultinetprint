<?php 
include_once 'format.php';

function get_name($ulti) {
	return str_to_str($ulti->get("system/name"));
}
function verify_auth($ulti) {
	$verify = msg_to_str($ulti->get("auth/verify"));
	if ($verify == "ok") return "Erfolgreich beim Drucker verifiziert. <img id='auth_img' src='images/erfolg.png' alt=''>";
	else if ($verify == "Authorization required.") return "Fehler bei der Verifizierung, ID/Key-Paar nicht valide. <img id='auth_img' src='images/löschen.png' alt=''>";
	else return "Fehler bei der Verifizierung, der Drucker ist womöglich ausgeschaltet oder nicht am Netz. <img id='auth_img' src='images/löschen.png' alt=''>";
}
function get_status($ulti) {
	$status = str_to_str($ulti->get("printer/status"));
	if ($status == "idle") return "im Leerlauf";
	else if ($status == "printing") {
		$print_time = str_to_str($ulti->get("print_job/time_total"));
		$time_elapsed = str_to_str($ulti->get("print_job/time_elapsed"));
		return "<div id='timer'><script> timer('" . date("M j, Y H:i:s", $print_time - $time_elapsed + time()) . "'); </script></div>";
	}
	else if ($status == "maintainance") return "im Wartungsmodus";
	else return $status;
}
function get_printname($ulti) {
	if (str_to_str($ulti->get("printer/status")) == "printing") return str_to_str($ulti->get("print_job/name"));
}
function get_printprogress($ulti) {
	if (str_to_str($ulti->get("printer/status")) == "printing") return round(str_to_str($ulti->get("print_job/progress")) * 100, 1) . "%";
}
function get_state($ulti) {
	return str_to_str($ulti->get("print_job/state"));
}
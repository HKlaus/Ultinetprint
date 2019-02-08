<?php
/**
*
* @author   Tom Lehmann
* @version  1.0
* 
*/
include_once 'format.php';

$data = $_GET["data"];			// Hole GET-Parameter

/**
* Sendet eine GET-Anfrage an den Ultimaker und gibt den Druckerstatus zurück, wird vom AJAX-Request aufgerufen
*
* @param string		$path 	Der API-Endpunkt an den die GET-Anfrage gesendet wird
*yy
* @return json
* 
*/
function get($path) {
	$curls = curl_init();			// Initiiere eine CURL-Session
	curl_setopt_array($curls, array(			// Setze die CURL Variablen
		CURLOPT_URL => "http://ultimaker3.informatik.hs-furtwangen.de/api/v1/" . $path,		// Setze den API-Endpunkt
		CURLOPT_RETURNTRANSFER => true,				// TRUE um den Transfer als String zurückzuliefern, anstatt ihn direkt auszugeben. 
		CURLOPT_TIMEOUT => 15,			// Die maximale Ausführungszeit in Sekunden für cURL-Funktionen. 
		CURLOPT_CONNECTTIMEOUT => 15,			// Die Anzahl Sekunden, die der Verbindungsaufbau maximal dauern darf
		CURLOPT_HTTPAUTH => CURLAUTH_DIGEST,		// Aktiviert die HTTP-Diggest Authentification
		CURLOPT_HEADER => false			// Ob der Header der Rückgabe mit ausgegeben werden soll
	));


	$json = curl_exec($curls);			// Führe die CURL-Session aus
	curl_close($curls);					// Schließe anschließend die CURL-Session
	return $json;
}

$status = str_to_str(get("printer/status"));			// Hole Drucker-Status
$state  = str_to_str(get("print_job/state"));			// Hole Drucker-State
	
if ($data == "status") {		// Wenn der AJAX Request den Status abfragt
	$print_time = str_to_str(get("print_job/time_total"));
	$time_elapsed = str_to_str(get("print_job/time_elapsed"));

	if ($status == "idle") echo "im Leerlauf";
	else if ($status == "printing") {
		if ($state == "printing") {
			echo "<div id='timer'><script> timer('" . date("M j, Y H:i:s", $print_time - $time_elapsed + time()) . "'); </script></div>";
		}  else echo "Warte..";
	}
	else if ($status == "maintainance") echo "im Wartungsmodus";
	else echo $status;
} elseif ($data == "name") {		// Wenn der AJAX Request den Drucknamen abfragt
	if ($status == "printing") {
		echo str_to_str(get("print_job/name"));
	}
} elseif ($data == "state") {		// Wenn der AJAX Request den State abfragt
	echo format_state($state);
} else echo $data;
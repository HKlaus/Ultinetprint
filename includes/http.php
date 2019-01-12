<?php
/**
*
* @author   Tom Lehmann
* @version  1.0
* 
*/

/**
* Sendet eine GET-Anfrage an den übergebenen Ultimaker
*
* @param Ultimaker3 $ulti 	Der zu verwendende Ultimaker
* @param string		$path 	Der API-Endpunkt an den die GET-Anfrage gesendet wird
*
* @return json
* 
*/
function get($ulti, $path) {
    $curls = curl_init();			// Initiiere eine CURL-Session
    curl_setopt_array($curls, array(			// Setze die CURL Variablen
        CURLOPT_URL => "http://" . $ulti->get_ip() . "/api/v1/" . $path,		// Setze den API-Endpunkt
        CURLOPT_RETURNTRANSFER => true,				// TRUE um den Transfer als String zurückzuliefern, anstatt ihn direkt auszugeben. 
        CURLOPT_TIMEOUT => 15,			// Die maximale Ausführungszeit in Sekunden für cURL-Funktionen. 
        CURLOPT_CONNECTTIMEOUT => 15,			// Die Anzahl Sekunden, die der Verbindungsaufbau maximal dauern darf
		CURLOPT_HTTPAUTH => CURLAUTH_DIGEST,		// Aktiviert die HTTP-Diggest Authentification
        CURLOPT_HEADER => false			// Ob der Header der Rückgabe mit ausgegeben werden soll
    ));
	if($ulti->get_id() and $ulti->get_key()) {
		curl_setopt($curls, CURLOPT_USERPWD, $ulti->get_id() . ":" . $ulti->get_key());		// Setzt HTTP Digest Credentials falls vorhanden
	} 

    $json = curl_exec($curls);			// Führe die CURL-Session aus
    curl_close($curls);					// Schließe anschließend die CURL-Session
    return $json;						// Gib die Anwort zurück
}

/**
* Sendet eine POST-Anfrage an den übergebenen Ultimaker
*
* @param Ultimaker3 $ulti 	Der zu verwendende Ultimaker
* @param string		$path 	Der API-Endpunkt an den die POST-Anfrage gesendet wird
* @param json		$data	Die zu sendenden Daten
*
* @return json
* 
*/
function post($ulti, $path, $data) {
    $curls = curl_init();
    curl_setopt_array($curls, array(
        CURLOPT_URL => "http://" . $ulti->get_ip() . "/api/v1/" . $path,
        CURLOPT_CUSTOMREQUEST => "POST",			// Setze den Anfragen-Typ auf "POST"
        CURLOPT_POSTFIELDS => $data,				// Setze die zu übertragenden Daten 
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 25,
        CURLOPT_CONNECTTIMEOUT => 25,
        CURLOPT_HTTPAUTH => CURLAUTH_DIGEST,
		CURLOPT_HTTPHEADER => array(                // Setze den HTTP-Header um den Content-Type "json" anzugeben                                                  
			'Content-Type: application/json',                                                                                
			'Content-Length: ' . strlen($data)),
        CURLOPT_HEADER => false
    ));
	if($ulti->get_id() and $ulti->get_key()) {
		curl_setopt($curls, CURLOPT_USERPWD, $ulti->get_id() . ":" . $ulti->get_key());
	} 

    $json = curl_exec($curls);
    curl_close($curls);
    return $json;
}

/**
* Sendet eine PUT-Anfrage an den übergebenen Ultimaker
*
* @param Ultimaker3 $ulti 	Der zu verwendende Ultimaker
* @param string		$path 	Der API-Endpunkt an den die PUT-Anfrage gesendet wird
* @param json		$data	Die zu sendenden Daten
*
* @return json
* 
*/
function put($ulti, $path, $data) {
    $curls = curl_init();
    curl_setopt_array($curls, array(
        CURLOPT_URL => "http://" . $ulti->get_ip() . "/api/v1/" . $path,
        CURLOPT_CUSTOMREQUEST => "PUT",
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_CONNECTTIMEOUT => 15,
        CURLOPT_HTTPAUTH => CURLAUTH_DIGEST,
		CURLOPT_HTTPHEADER => array(                                                                          
			'Content-Type: application/json',                                                                                
			'Content-Length: ' . strlen($data)),
        CURLOPT_HEADER => false
    ));
	if($ulti->get_id() and $ulti->get_key()) {
		curl_setopt($curls, CURLOPT_USERPWD, $ulti->get_id() . ":" . $ulti->get_key());
	} 

    $json = curl_exec($curls);
    curl_close($curls);
    return $json;
}

/**
* Sendet eine multipart-Datei per POST-Anfrage an den übergebenen Ultimaker
*
* @param Ultimaker3 $ulti 		Der zu verwendende Ultimaker
* @param string		$filename 	Die Datei die gesendet werden soll
*
* @return bool
* 
*/
function post_file($ulti, $filename) {
	$path = "uploads/gcode/" . $filename;				// Setze den vollständigen Pfad mit Dateinamen 
	$data = [ 'file' => new CurlFile($path, "application/octet-stream", $filename) ];			// Erstelle eine neue CURL-Datei
	
    $curls = curl_init();
    curl_setopt_array($curls, array(
        CURLOPT_URL => "http://" . $ulti->get_ip() . "/api/v1/print_job",
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 360,							// Erhöhe den Timeout im Gegensatz zu den vorherigen Funktionen, z.B. für sehr große Dateien
        CURLOPT_CONNECTTIMEOUT => 360,
        CURLOPT_HTTPAUTH => CURLAUTH_DIGEST,
		CURLOPT_HTTPHEADER => array(                                                                          
			"Content-Type: multipart/form-data"),		// Setze den Content-Type auf eine multipart-Datei
        CURLOPT_HEADER => false
    ));
	if($ulti->get_id() and $ulti->get_key()) {
		curl_setopt($curls, CURLOPT_USERPWD, $ulti->get_id() . ":" . $ulti->get_key());
	} 
    curl_exec($curls);	
    curl_close($curls);
	
	if (!is_printing($ulti)) {							// Wenn die Übertragung nicht geklappt haben sollte gibt diese Funktion false zurück
		return false;					
	}

    return true;										// Wenn Übertragung erfolgreich, gib true zurück
}
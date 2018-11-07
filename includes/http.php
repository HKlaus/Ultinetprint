<?php

function get($ulti, $path) {
    $curls = curl_init();
    curl_setopt_array($curls, array(
        CURLOPT_URL => "http://" . $ulti->get_ip() . "/api/v1/" . $path,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_CONNECTTIMEOUT => 5,
		CURLOPT_HTTPAUTH => CURLAUTH_DIGEST,
        CURLOPT_HEADER => false
    ));
	if($ulti->get_id() and $ulti->get_key()) {
		curl_setopt($curls, CURLOPT_USERPWD, $ulti->get_id() . ":" . $ulti->get_key());		// Setzt HTTP Digest Credentials falls vorhanden
	} 

    $json = curl_exec($curls);
    curl_close($curls);
    return $json;	
}

function post($ulti, $path, $data) {
    $curls = curl_init();
    curl_setopt_array($curls, array(
        CURLOPT_URL => "http://" . $ulti->get_ip() . "/api/v1/" . $path,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_CONNECTTIMEOUT => 5,
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

function put($ulti, $path, $data) {
    $curls = curl_init();
    curl_setopt_array($curls, array(
        CURLOPT_URL => "http://" . $ulti->get_ip() . "/api/v1/" . $path,
        CURLOPT_CUSTOMREQUEST => "PUT",
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_CONNECTTIMEOUT => 5,
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

function post_file($ulti, $filename) {
	$path = "uploads/gcode/" . $filename;
	$data = [ 'file' => new CurlFile($path, "application/octet-stream", $filename) ];
	
    $curls = curl_init();
    curl_setopt_array($curls, array(
        CURLOPT_URL => "http://" . $ulti->get_ip() . "/api/v1/print_job",
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_HTTPAUTH => CURLAUTH_DIGEST,
		CURLOPT_HTTPHEADER => array(                                                                          
			"Content-Type: multipart/form-data"),
        CURLOPT_HEADER => false
    ));
	if($ulti->get_id() and $ulti->get_key()) {
		curl_setopt($curls, CURLOPT_USERPWD, $ulti->get_id() . ":" . $ulti->get_key());
	} 

    $json = curl_exec($curls);
    curl_close($curls);
	echo is_printing($ulti);
    return $json;
}
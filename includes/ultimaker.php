<?php
/**
* @author   Tom Lehmann
* @version  1.0
* 
*/

include_once 'user.php';

/**
* Diese Klasse repräsentiert einen 3D-Drucker
* 
*/
class Ultimaker3 {

	private $app;				// Für Ultimaker API benötigter Appname
	private $appuser;			// Für Ultimaker API benötigter Nutzer
	private $ip;				

	private $id;				// Für Ultimaker API benötigte ID
	private $key;				// Für Ultimaker API benötigter Key
	
	private $user;
	private $mysqli;
	
	/**
	* Konstruktor, der aufgerufen wird wenn die eine Instanz erzeugt wird
	* 
	*/
	function __construct ($mysqli, $app, $appuser, $ip) {
		$this->user  = new User($mysqli);
		$this->mysqli = $mysqli;
		$this->app = $app;
		$this->appuser = $appuser;
		$this->ip = $ip;
		$auth = req_auth($mysqli);
		$this->id = $auth[0];
		$this->key= $auth[1];
	}
	
	/**
	* Getter
	* 
	*/
	function get_ip() { return $this->ip; }
	function get_id() { return $this->id; }
	function get_key() { return $this->key; }
	function get_user() { return $this->user; }
	
	
	
	/**
	* Funktionen, die die HTTP-Anfragen an den Drucker senden
	* 
	* @param	string	$path	API-Endpunkt
	* @param 	json	$data	Zu sendende Daten
	*
	* @return	json
	*/
	function get($path) {
		return get($this, $path);
	}
	function post($path, $data) {
		printer_history($this->mysqli, $this->user->get_user_id(), "post", $path, $data);
		return post($this, $path, $data);
	}
	function put($path, $data) {
		printer_history($this->mysqli, $this->user->get_user_id(), "put", $path, $data);
		return put($this, $path, $data);
	}
	function post_file($path) {
		printer_history($this->mysqli, $this->user->get_user_id(), "post_file", "/print_job", $path);
		return post_file($this, $path);
	}
}
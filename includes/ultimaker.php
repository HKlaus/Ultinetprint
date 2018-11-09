<?php

include_once 'user.php';

class Ultimaker3 {

	private $app		= "thesis";
	private $appuser 	= "webservice";
	private $ip 		= "141.28.73.22";

	private $id;
	private $key;
	
	private $user;
	private $mysqli;
	
	function __construct ($mysqli) {
		$this->user  = new User($mysqli);
		$this->mysqli = $mysqli;
		$auth = req_auth($mysqli);
		$this->id = $auth[0];
		$this->key= $auth[1];
	}
	function get_ip() { return $this->ip; }
	function get_id() { return $this->id; }
	function get_key() { return $this->key; }
	function get_user() { return $this->user; }
	function req_auth() {
		$path = "/auth/request";
		$data = "application=" . $this->app . "&user=" . $this->appuser;
		$json = post($this, $path, $data);		
		$this->id = json_decode($json)->{'id'};
		$this->key = json_decode($json)->{'key'};
		return $json;
	}
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
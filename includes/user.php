<?php

class User {
	private $user_id = "";
	private $user_email = "";
	private $user_level = "";
	
	function __construct($mysqli) {
		 $this->user_id = login_check($mysqli);
		 $this->user_email = email_check($mysqli);
		 $this->user_level = admin_check($mysqli);
	}
	function get_user_id() { return $this->user_id; }
	function get_user_email() { return $this->user_email; }
	function get_user_level() { return $this->user_level; }
}
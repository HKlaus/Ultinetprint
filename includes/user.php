<?php
/**
*
* @author   Tom Lehmann
* @version  1.0
* 
*/

/**
* Diese Klasse repräsentiert einen Benutzer
* 
*/
class User {
	private $user_id;				// eindeutige ID
	private $user_email;			// Email
	private $user_level;			// Betreuerrechte
	private $print_rights;			// Druckrechte
	
	/**
	* Konstruktor, der aufgerufen wird wenn eine Instanz erzeugt wird
	* 
	*/
	function __construct($mysqli) {
		 $this->user_id = login_check($mysqli);
		 $this->user_email = email_check($mysqli);
		 $this->user_level = admin_check($mysqli);
		 $this->print_rights = active_check($mysqli);
	}
	
	/**
	* Getter
	* 
	*/
	function get_user_id() { return $this->user_id; }
	function get_user_email() { return $this->user_email; }
	function get_user_level() { return $this->user_level; }
	function get_print_rights() { return $this->print_rights; }
}
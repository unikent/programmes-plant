<?php
class Staff extends SimpleData
{
	public static $table = 'research_staff';

	public static $rules = array();

	// Use all these fields when producing a "list box"
	public static $list_fields = array('id', 'login', 'forename', 'surname', 'title');

	// Load user data from ldap, using users login name
	public function load_from_ldap(){
		// Attempt to load user from ldap
		$ldap = LDAPConnect::instance();
		$attributes = $ldap->getUserAnonymous($this->login);
		// If attributes were returned, use them
		if($attributes != false){
			// title?
			$this->email = isset($attributes[0]['mail'][0]) ? $attributes[0]['mail'][0] : ''; 
			$this->forename = isset($attributes[0]['givenname'][0]) ? $attributes[0]['givenname'][0] : '';
			$this->surname = isset($attributes[0]['sn'][0]) ? $attributes[0]['sn'][0] : ''; 
		}
	}

	// Load LDAP attributes on save
	public function save(){
		// populate ldap attributes
		$this->load_from_ldap();

		parent::save();
	}

	// use "Login" instead of name as title
	public static function get_title_field(){return 'login';}


	public function get_login(){return $this->get_name();}


	// Pretty print name when requested
	public function get_name(){
		return $this->attributes['title'].' '.$this->attributes['forename'].' '.$this->attributes['surname'].' ('.$this->attributes['login'].')';
	}
}
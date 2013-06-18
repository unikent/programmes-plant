<?php
class Staff extends SimpleData
{
	public static $table = 'research_staff';

	public static $rules = array();

	// place holder class for staff datamodel? may end up being built on WS or somthing rather than DB in PP itself?

	//public static function get() {return array(); }
	//public static function all_as_list() {return array(); }


	public function get_name(){
		return $this->title.' '.$this->forename.' '.$this->surname.' ('.$this->login.')';
	}

	public function load_from_ldap(){

		echo $this->login;
		// Attempt to load user from ldap
		$ldap = LDAPConnect::instance();
		$attributes = $ldap->getUserAnonymous($this->login);

		if($attributes != false){
			// title?
			$this->email = isset($attributes[0]['mail'][0]) ? $attributes[0]['mail'][0] : ''; 
			$this->forename = isset($attributes[0]['givenname'][0]) ? $attributes[0]['givenname'][0] : '';
			$this->surname = isset($attributes[0]['sn'][0]) ? $attributes[0]['sn'][0] : ''; 
		}
	}

	public function save(){
		// populate ldap attributes
		$this->load_from_ldap();

		parent::save();
	}
}
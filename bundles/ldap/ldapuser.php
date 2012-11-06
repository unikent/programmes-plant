<?php

/**
 * Class returned from LDAP::getUserObject().
 * Provides quick methods to get basic user details.
 */
class LDAPUser {

	private $info;
	private $ldap;

	/**
	 * Contructor
	 * @param array $info A users info.
	 */
	public function __construct($info, $ldap) {
		$this->info = $info;
		$this->ldap = $ldap;
	}

	/**
	 * Get a user's full/display name.
	 * @return string Users full/display name
	 */
	public function getDisplayName() {
		return $this->info['displayname'][0];
	}


	/**
	 * Get an Attribute from a user.
	 *
	 * @param string Attribute name.
	 * @return string Value of Attribute
	 */
	public function getAttribute($attr) {
		if(isset($this->info[$attr][0])){
			return $this->info[$attr][0];
		}else{
			return null;
		}
	}

	/**
	 * Get user's username.
	 * @return string User's username
	 */
	public function getUserName() {
		return $this->info['uid'][0];
	}

	/**
	 * Get user's Email.
	 * @return string User's username
	 */
	public function getEmail() {
		return $this->info['mail'][0];
	}

	/**
	 * Is this user a Student
	 * @return Boolean
	 */
	public function isStudent(){
		return ($this->getAccountType()=='Student');
	}

	/**
	 * Is this user an Alumni
	 * @return Boolean
	 */
	public function isAlumni(){
		return ($this->getAccountType()=='Alumni');
	}

	/**
	 * Is this user a Staff Member
	 * @return Boolean
	 */
	public function isStaff(){
		return ($this->getAccountType()=='Staff');
	}

	/**
	 * Get user Type.
	 * @return string User's Type based on there DN
	 */
	public function getAccountType() {
		if(strpos($this->info['dn'],'ou=students') != false){
			return 'Student';
		}else if(strpos($this->info['dn'],'ou=staff') != false){
			return 'Staff';
		}else if(strpos($this->info['dn'],'ou=alumni') != false){
			return 'Alumni';
		}else{
			return 'Unknown';
		}
	}

}
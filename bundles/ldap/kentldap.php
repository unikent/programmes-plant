<?php use \Config;
/**
 * LDAP Class provides basic operations for authenticating users and getting details from them.
 *
 * @author Carl Saggs (based on Rikki Carroll's original LDAP Class)
 * @version 2011.10.05
 */

class KentLDAP {
	// Stores the address of the LDAP server
	private $ldap_server_address;
	private $ldap_connection;

	// Auth
	private $ldap_base_user ='';
	private $ldap_base_pass ='';

	private static $instance = null;

	public static function instance(){
		if(self::$instance == null){
			self::$instance = new KentLDAP(Config::get('ldap.host'), Config::get('ldap.port'));
			self::$instance->setBaseRDN(Config::get('ldap.base_dn'));
			return self::$instance;
		}else{
			return self::$instance;
		}

	}

	/**
	 * Creates a instance of the LDAP class.
	 * Requires one parameter - the address of the LDAP server.
	 *
	 * @param $address string The address of the LDAP server.
	 */
	public function __construct($address,$port=389) {
		$this->ldap_server_address = $address;
		$this->ldap_connection = ldap_connect($address, $port);
	}

	/**
	 * Set the base user for LDAP
	 * 
	 * @param $user (full dsn)
	 * @param $password 
	 */
	public function setBaseUser($user,$pass){
		$this->ldap_base_user = $user;
		$this->ldap_base_pass = $pass;
	}
	
	/**
	 * Set the base user for RDN
	 * 
	 * @param $rdn Location to search for required feilds in
	 */
	public function setBaseRDN($rdn){
		$this->ldap_base_rdn = $rdn;
	}

	/**
	 * Returns whether the current LDAP Request object is connected to a server.
	 * 
	 * @return True if connected, false otherwise.
	 */
	public function isConnected() {
		if ($this->ldap_connection) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Disconnects any current connection to an LDAP server.
	 */
	public function disconnect() {
		@ldap_close($this->ldap_connection);
	}

	/**
	 * Returns the address of the current LDAP server address.
	 * 
	 * @returns The string representing the address of the LDAP server.
	 */
	public function getLDAPServerAddress()
	{
		return $this->ldap_server_address;
	}
	
	/**
	 * Run automatically by PHP when the object is removed from memory.
	 * Here we basically clean up and close the connection to the ldap server.
	 */
	public function __destruct() {
		$this->disconnect();
		unset($this->ldap_server_address);
		unset($this->ldap_connection);
	}

	/**
	 * Return infomation from a user based on there UID
	 * 
	 *  @param $uid
	 *  @return array
	 */
	public function getDetails($username) {
		return $this->ldap_query($username);
	}

	public function getError(){
		if($this->ldap_connection == null) return "no ldap connection";

		return ldap_error($this->ldap_connection);
	}

	/**
	 * Get a user's DN
	 * 
	 * @param UID
	 * @return Fully qualifed DN of user (or null if they do not exist/cannot be found)
	 */
	private function getUserDn($uid){
		//Bind Request
		$this->createRequest($this->ldap_connection, $this->ldap_base_user, $this->ldap_base_pass);
		
		//look em up
		$sr = ldap_search($this->ldap_connection, $this->ldap_base_rdn, "uid={$uid}");
		//get specific user
		$first = ldap_first_entry($this->ldap_connection, $sr);
		if($first===false)return null;
		return  ldap_get_dn($this->ldap_connection, $first);

	}

	/**
	 * Ensure base user is set before carrying out dependant operations
	 * 
	 * @throws Exception When base user is not set
	 *
	 */
	private function checkBaseUser(){
		if($this->ldap_base_user =='' || $this->ldap_base_pass =='' || $this->ldap_base_user == null){
			throw new Exception("Base user has not been defined. A base user is required in order to perform read operations on users.");
		}
	}

	/**
	 * Get a user by username
	 * 
	 * @param $username
	 * @return user as a LDAPUser Object
	 *
	 */
	function getUserObject($username) {

		$details = $this->getDetails($username);
		if(!isset($details['error'])){
			return new LDAPUser($details[0], $this);
		}else{
			return null;
		}
	}

	/**
	 * Validate username and Password Against LDAP
	 * 
	 * @param string $username
	 * @return bool If authentication was succesful returns true
	 */
	public function authenticateUser($username, $password) {
		$ds = $this->ldap_connection;

		if ($ds) {
			//when binding password must not be empty, otherwise the it will validate based on username only
			if (empty($password)) $password = " ";

			//get user string (+ return false if user doesn't exist)
			$user_dn = $this->getUserDn($username);

			if($user_dn == null) return false;

			//Attempt to Bind 
			$r = $this->createRequest($ds, $user_dn ,$password);

			if($r) {
				return true;
			}
		}
		return false;
	}
	
	public function getAuthenticateUser($username, $password) {
		$ds = $this->ldap_connection;
		
		if ($ds) {
			//when binding password must not be empty, otherwise the it will validate based on username only
			if(empty($password)) $password = " ";

			// Get user string (or return false if user doesn't exist)
			$user_dn = $this->getUserDn($username);
			if ($user_dn == null) return false;

			// Attempt to Bind 
			$r = $this->createRequest($ds, $user_dn ,$password);

			if($r) {
				$sr = ldap_search($this->ldap_connection, $this->ldap_base_rdn, "uid={$username}");
				return ldap_get_entries($this->ldap_connection, $sr);
			}
		}
		return false;
	}

	/**
	 * Connect to LDAP with given credentals. (Bind)
	 *
	 * @param $ds LDAP Connection
	 * @param $usr User(full dn) to bind to ldap with
	 * @param $pass password to autenticate ldap with
	 */
	private function createRequest($ds, $usr, $pass) {
		ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);

		if (@ldap_bind($ds, $usr, $pass)){
			return true;
		}
		else{
			return false;//Return false rather than throwing error
		}
	}
}

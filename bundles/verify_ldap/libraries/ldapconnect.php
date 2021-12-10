<?php use \Laravel\Config;
/**
 * LDAP Class provides basic operations for authenticating users and getting details from them.
 *
 * This is an object orientated wrapper for the LDAP PHP internals.
 *
 * @author Carl Saggs (based on Rikki Carroll's original LDAP Class), Alex Andrews.
 * @version 2011.10.05
 */

class LDAPConnect
{
    // Stores the address of the LDAP server
    private $ldap_server_address;
    private $ldap_connection;

    private static $instance = null;

    public static function instance()
    {
        if (self::$instance == null) {
            self::$instance = new LDAPConnect(Config::get('ldap.host'), Config::get('ldap.port'));
            self::$instance->setBaseRDN(Config::get('ldap.base_dn'));

            return self::$instance;
        } else {
            return self::$instance;
        }

    }

    /**
     * Creates a instance of the LDAP class.
     * Requires one parameter - the address of the LDAP server.
     *
     * @param string $address The address of the LDAP server.
     * @param int    $port    The port LDAP is on.
     */
    public function __construct($address, $port = 389)
    {
        $this->ldap_server_address = $address;
        // use connect string rather than depricated address and port params to work
        // around issue with using non-default port
        $this->ldap_connection = ldap_connect("$address:$port");
    }

    /**
     * Set the base user for RDN
     *
     * @param $rdn Location to search for required feilds in
     */
    public function setBaseRDN($rdn)
    {
        $this->ldap_base_rdn = $rdn;
    }

    /**
     * Returns whether the current LDAP Request object is connected to a server.
     *
     * @return bool True if connected, false otherwise.
     */
    public function isConnected()
    {
        if ($this->ldap_connection) {
            return true;
        }

        return false;
    }

    /**
     * Disconnects any current connection to an LDAP server.
     *
     * @return void
     */
    public function disconnect()
    {
        @ldap_close($this->ldap_connection);
    }

    /**
     * Returns the address of the current LDAP server address.
     *
     * @return The string representing the address of the LDAP server.
     */
    public function getLDAPServerAddress()
    {
        return $this->ldap_server_address;
    }

    /**
     * Run automatically by PHP when the object is removed from memory.
     *
     * Here we basically clean up and close the connection to the LDAP server.
     */
    public function __destruct()
    {
        $this->disconnect();
        unset($this->ldap_server_address);
        unset($this->ldap_connection);
    }

    /**
     * Return the LDAP error from the connection for last run command.
     *
     * @return string The LDAP error message.
     */
    public function getError()
    {
        if($this->ldap_connection == null) return "No ldap connection";

        return ldap_error($this->ldap_connection);
    }

    /**
     * Get a user's DN
     *
     * @param UID
     * @return Fully qualifed DN of user (or null if they do not exist/cannot be found)
     */
    private function getUserDn($uid)
    {
        if (! $this->ldap_connection) {
            return false;
        }

        // Look them up
        $sr = ldap_search($this->ldap_connection, $this->ldap_base_rdn, "uid={$uid}");

        // Get specific user
        $first = ldap_first_entry($this->ldap_connection, $sr);

        if($first===false) return null;

        return  ldap_get_dn($this->ldap_connection, $first);
    }

    /**
     * Validate username and password against LDAP.
     *
     * @param  string $username
     * @return bool   If authentication was succesful returns true
     */
    public function authenticateUser($username, $password)
    {
        $ds = $this->ldap_connection;

        if ($ds) {
            //when binding password must not be empty, otherwise the it will validate based on username only
            if (empty($password)) $password = " ";

            //get user string (+ return false if user doesn't exist)
            $user_dn = $this->getUserDn($username);

            if($user_dn == null) return false;

            //Attempt to Bind
            $r = $this->createRequest($ds, $user_dn ,$password);

            if ($r) {
                return true;
            }
        }

        return false;
    }

    /**
     * Authenticate user and return their details from LDAP.
     *
     * @param  string $username The username of the user.
     * @param  string $password The password of the user.
     * @return bool   | array False if the user is not authenticated. An array of their LDAP entries if they are.
     */
    public function getAuthenticateUser($username, $password)
    {
        $ds = $this->ldap_connection;

        if ($ds) {
            //when binding password must not be empty, otherwise the it will validate based on username only
            if(empty($password)) $password = " ";

            // Get user string (or return false if user doesn't exist)
            $user_dn = $this->getUserDn($username);

            if ($user_dn == null) return false;

            // Attempt to Bind
            $r = $this->createRequest($ds, $user_dn ,$password);

            if ($r) {
                $sr = ldap_search($this->ldap_connection, $this->ldap_base_rdn, "uid={$username}");

                return ldap_get_entries($this->ldap_connection, $sr);
            }
        }

        return false;
    }

     /**
     * Anonymous get a users details from LDAP (no auth required)
     *
     * @param  string $username The username of the user.
     * @return bool   | array False if the user is not authenticated. An array of their LDAP entries if they are.
     */
    public function getUserAnonymous($username){
        $ds = $this->ldap_connection;
        if ($ds) {
            // bind anonymously
             $r = $this->createRequest($ds, null, null);

             if($r){
                $sr = ldap_search($this->ldap_connection, $this->ldap_base_rdn, "uid={$username}");
                return ldap_get_entries($this->ldap_connection, $sr);
             } 
        }
        return false;
    }

    /**
     * Connect to LDAP with given credentals. (Bind)
     *
     * @param $ds LDAP Connection.
     * @param string $usr  User (full DN) to bind to ldap with.
     * @param string $pass Password to autenticate ldap with.
     */
    private function createRequest($ds, $usr, $pass)
    {
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);

        if (@ldap_bind($ds, $usr, $pass)) {
            return true;
        } else {
            return false; //Return false rather than throwing error
        }
    }
}

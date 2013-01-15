<?php use \Session, \Exception;
/**
 * Logs the user in using LDAP.
 * 
 * Uses the our own LDAP library.
 */
class LDAP extends \Laravel\Auth\Drivers\Driver {
	
	// KentLDAP object
	public $ldap = false;

	public function __construct()
	{
		if (Session::has('user'))
		{
			$this->user = Session::get('user');
		}

		parent::__construct();
	}

	/**
	 * Get the user of the application.
	 * 
	 * Returns null if the user is a guest.
	 *
	 * @return mixed|null
	 */
	public function user()
	{	
		if (! $this->user)
		{
			return null;
		}
		else
		{
			return $this->user->id;
		}

	}

	/**
	 * Log the user out.
	 * 
	 * @return void
	 */
	public function logout()
	{
		// Chuck away everything stored in the session.
		Session::flush();

		// Call a disconnection from Kent LDAP.
		if ($this->ldap)
		{
			$this->ldap->disconnect();
		}
		
		// Chuck everything in memory.
		unset($this->ldap);
		unset($this->user);
	}
	
	/**
	 * Get the current user of the application.
	 *
	 * If the user is a guest, null should be returned.
	 *
	 * @param  string $id
	 * @return mixed|null
	 * @todo Do something with their ID.
	 */
	public function retrieve($id)
	{
		if (! $this->user)
		{
			return false;
		}
		else
		{

			return $this->user;
		}
	}

	/**
	 * Get the full name of the user.
	 *
	 * @return string $fullname Full name of the user.
	 */
	public function fullname()
	{
		if (is_null($this->user()))
		{
			throw new Exception('User is not authenticated, could not retrieve full name.');
		}

		return $this->user->fullname;
	}
	
	/**
	 * Attempt to log a user into the application
	 *
	 * @param  array $arguments
	 * @return void
	 */
	public function attempt($arguments = array())
	{
		$username = $arguments['username'];
		$password = $arguments['password'];

		$this->ldap = LDAPConnect::instance();
		$user = $this->ldap->getAuthenticateUser($username, $password);

		if ($user !== false)
		{
			$userObject = new stdClass();
			$userObject->id = $username;
			$userObject->username = $username;
			$userObject->name = $user[0]['givenname'][0];
			$userObject->fullname = $user[0]['unikentaddisplayname'][0];
			$userObject->email = $user[0]['mail'][0];
			$userObject->title = isset($user[0]['title'][0]) ? $user[0]['title'][0] : '';

			// Get role
			$role = Role::where('username', '=', $username)->first();

			// If role doesnt exist, create one.
			if ($role == null)
			{
				$role = new Role;
		        $role->username = $username;
		        $role->fullname = $userObject->fullname;
		        $role->isadmin  = false;
		        $role->isuser   = false;
		        $role->department = isset($user[0]['unikentoddepartment'][0]) ? $user[0]['unikentoddepartment'][0] : '';
		        $role->save();		
			}
			
			// Set to user object
			$userObject->dept = $role->department;
			$userObject->isadmin = $role->isadmin;
			$userObject->isuser = $role->isuser;
			$userObject->internal_id = $role->id;

			Session::put('user', $userObject);

			return $this->login($username, array_get($arguments, 'remember'));
		}
		
		Session::flash('status', "Autentication failed");
	}

}
<?php use \Session;
/**
 * The University of Kent Single Sign On
 * 
 * Logs a user into Laravel using Kent Single Sign On.
 * 
 * Uses SimpleSAML-PHP library that must be installed in the vendor Laravel path.
 * 
 * @author Justice Addison <j.addison@kent.ac.uk> and Alex Andrews <a.andrews@kent.ac.uk>
 */
class LDAP extends \Laravel\Auth\Drivers\Driver {

	/**
	 * Get the username of the application.
	 *
	 * @param  string $id
	 * @return mixed|null
	 */
	public function user()
	{
		if($id != '' && Session::has($id)){
			return Session::get($id);
		}
	}

	/**
	 * Log the user out
	 * 
	 * @return void
	 */
	public function logout($url = null)
	{
		Session::flush();	
	}
	
	/**
	 * Get the current user of the application.
	 *
	 * If the user is a guest, null should be returned.
	 *
	 * @param  string $id
	 * @return mixed|null
	 * @todo Is this correctly implemented?
	 */
	public function retrieve($id)
	{
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
		$ldap = KentLDAP::instance();
		$usr = $ldap->getAuthenticateUser($username, $password);

		if($usr !== false){
			$userObject = new stdClass();
			$userObject->id = $username;
			$userObject->username = $username;
			$userObject->name = $usr[0]['givenname'][0];
			$userObject->fullname = $usr[0]['unikentaddisplayname'][0];
			$userObject->email = $usr[0]['mail'][0];
			$userObject->title = $usr[0]['title'][0];

			//get role
			$role = Role::where('username','=',$username)->first();

			//If role doesnt exist, createe em
			if($role == null){
				$role = new Role;
		        $role->username = $username;
		        $role->fullname = $userObject->fullname;
		        $role->isadmin  = false;
		        $role->isuser   = false;
		        $role->department = $usr[0]['unikentoddepartment'][0];
		        $role->save();		
			}
			
			//set to user object
			$userObject->dept 		= $role->department;
			$userObject->isadmin 	= $role->isadmin;
			$userObject->isuser 	= $role->isuser;
			$userObject->internal_id= $role->id;

			Session::put($username, $userObject);
			Session::put('flash', "Logged in as: ". $username);

			return $userObject;
		}
		
		Session::flash('flash', "Autentication failed");
	}

}
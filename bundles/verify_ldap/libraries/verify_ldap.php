<?php

/**
 * Verification Library
 *
 * @author Todd Francis
 * @version 3.0.0
 */

class verify_ldap extends Verify
{

	/**
	 * __construct
	 */
	public function __construct()
	{
	
		// Setup vars this will end up using
		Config::set('verify::verify.user_model', 'Verify\Models\User');;
		Config::set('verify::verify.prefix', 'usersys');
		Config::set('verify::verify.super_admin', 'Administrator');


		parent::__construct();
	}

	/**
	 * Attempt to log a user into the application.
	 *
	 * @param  array  $arguments
	 * @return void
	 */
	public function attempt($arguments = array())
	{
		$valid = false;


		// Get username / Password
		$username = $arguments['username'];
		$password = $arguments['password'];

		// Boot LDAP
		$this->ldap = LDAPConnect::instance();
		$ldap_user = $this->ldap->getAuthenticateUser($username, $password);

		if ($ldap_user !== false)
		{
			// User has a valid kent username & password.

			// Do they exist in the system now?
			$db_user = $this->model()->where('username', '=', $arguments['username'])->first();

			if (!is_null($user))
			{
				// User exists in the system


			}
			throw new UserNotFoundException('User can not be found');
		}
		// Not in LDAP, then fail
		throw new UserPasswordIncorrectException('User password is incorrect');
		


			/*

		// Get the username fields
		$usernames = Config::get('verify::verify.username');
		$usernames = (!is_array($usernames))
			? array($usernames)
			: $usernames;

		foreach ($usernames as $identify_by)
		{
			$user = $this->model()
				->where($identify_by, '=', array_get($arguments, $identify_by))
				->first();

			if (!is_null($user))
			{
				// Is user password is valid?
		                if(!Hash::check($user->salt . array_get($arguments, 'password'), $user->password))
		                {
		                    throw new UserPasswordIncorrectException('User password is incorrect');
		                }

				// Valid user, but are they verified?
				if (!$user->verified)
				{
					throw new UserUnverifiedException('User is unverified');
				}

				// Is the user disabled?
				if ($user->disabled)
				{
					throw new UserDisabledException('User is disabled');
				}

				// Is the user deleted?
				if ($user->deleted)
				{
					throw new UserDeletedException('User is deleted');
				}

				$valid = true;
				break;
			}
		}
		*/

		if ($valid)
		{
			return $this->login($user->id, array_get($arguments, 'remember'));
		}
		else
		{
			throw new UserNotFoundException('User can not be found');
		}
	}

}
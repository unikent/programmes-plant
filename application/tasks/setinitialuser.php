<?php
/**
 * SetInitalUser Sets the default user on the system.
 * This is used for dev systems only and is disabled when a system is live.
 * Usernames should match those specified in ldap to allow logging in to the system
 *
 * Example: php artisan setinitaluser myname --env=local
 */
class SetInitialUser_Task {

	/**
	 * Run the setinitaluser task
	 * 
	 * @param array  $arguments The arguments sent to the seed command.
	 */
	public function run($arguments = array())
	{	
		// Dont run if no username provided
		if(sizeof($arguments) === 0){
			echo "\n Error: Please specify your LDAP username. \n";
			return;
		} 
		// Dont run if not new system
		if(sizeof(DB::table('usersys_users')->get()) !== 1){
			echo "\n Error: For securty reasons set inital user will only run when only the initial user is present in the DB \n";
			return;
		}

		$username = $arguments[0];

		// Update username
		DB::table('usersys_users')->where('id', '=', 1)->update(array(
			'username'			=> $username,
		));
		
		// Confirm success
		echo "\n Initial user set as {$username}. You can now log in. \n";
	}
}
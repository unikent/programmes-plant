<?php namespace Tests;

/**
* a wrapper for creating a new test user in the verify permissions framework
*/
class TestUser {

	/**
	* creates a test user in a test role with the specified permissions
	*
	* @param $permissions array()
	* @return $user object
	*/
	public static function create_with_permissions($permissions=array())
	{
		// create a new role
		$role = new \Role;
		$role->name = 'User';
		$role->level = 1;
		$role->save();

		// assign permissions to the role
		// get the appropriate permission we want to test from the db
		foreach ($permissions as $permission)
		{
			$permission_obj = \Permission::where_name($permission)->first();
			// assign the permissions to the role
			$role->permissions()->sync(array($permission_obj->id));
		}

		// set up a user in the role
		$user = new \User;
		$user->username = 'test';
		$user->fullname = 'test';
		$user->subjects = '';
		$user->email = 'a.test@kent.ac.uk';
		$user->verified = 1;
		$user->save();
		$user->roles()->sync(array($role->id));

		return $user;
	}

}
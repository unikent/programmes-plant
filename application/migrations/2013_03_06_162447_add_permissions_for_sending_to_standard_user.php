<?php

use \Verify\Models\Role as Role;
use \Verify\Models\Permission as Permission;

class Add_Permissions_For_Sending_To_Standard_User {

	public function __construct()
	{
		Config::set('verify::verify.prefix', 'usersys');
	}

	/**
	 * Add permissions for sending to the standard user role.
	 *
	 * @return void
	 */
	public function up()
	{
		$user_role = Role::where('name', '=', 'User')->first();
		$permission = Permission::where('name', '=', 'submit_programme_for_editing')->first();

		$user_role->permissions()->attach($permission->id);
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		$user_role = Role::where('name', '=', 'User');
		$permission = Permission::where('name', '=', 'submit_programme_for_editing')->first();

		$user_role->permissions()->detach($permission->id);
	}

}
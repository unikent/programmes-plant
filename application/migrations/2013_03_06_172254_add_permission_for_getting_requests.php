<?php

class Add_Permission_For_Getting_Requests {

	public function __construct()
	{
		Config::set('verify::verify.prefix', 'usersys');
	}

	/**
	 * Add the permision for recieving requests and add this to the Admin user.
	 *
	 * @return void
	 */
	public function up()
	{
		$admin_role = Role::where('name', '=', 'Admin')->first();
		
		$permission = new Permission(array('name' => 'recieve_edit_requests'));

		$admin_role->permissions()->insert($permission);
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		$permission = Permission::where('name', '=', 'recieve_edit_requests')->first();
		$admin_role = Role::where('name', '=', 'Admin')->first();

		$admin_role->permissions()->detach($permission->id);
		
		$permission->delete();
	}

}
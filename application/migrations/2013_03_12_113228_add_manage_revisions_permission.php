<?php

use \Verify\Models\Role as Role;
use \Verify\Models\Permission as Permission;

class Add_Manage_Revisions_Permission {

	public function __construct()
	{
		Config::set('verify::verify.prefix', 'usersys');
	}
	/**
	 * Add the additional 'make_programme_live' permission to the admin user.
	 *
	 * @return void
	 */
	public function up()
	{
		$admin_user = Role::where_name('Admin')->first();

		$permission = new Permission(array('name' => 'delete_programmes'));
		$permission->save();

		$admin_user->permissions()->attach($permission);
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		$admin_user = Role::where_name('Admin')->first();

		$permission = Permission::where_name('delete_programmes')->first();
		
		$admin_user->permissions()->detach($permission);
		
		$permission->delete();
	}

}
<?php

use \Verify\Models\Role as Role;
use \Verify\Models\Permission as Permission;
	
class Add_Permissions_To_Admin_User {

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
		$permission = Permission::where_name('make_programme_live')->first(array('id'));

		$admin_user->permissions()->attach($permission->id);
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		$admin_user = Role::where_name('Admin')->first();
		$permission = Permission::where_name('make_programme_live')->first(array('id'));

		$this->admin_user->permissions()->detach($permission->id);
	}

}
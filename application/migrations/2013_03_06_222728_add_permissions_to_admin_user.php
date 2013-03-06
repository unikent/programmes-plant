<?php

use \Verify\Models\Role as Role;
use \Verify\Models\Permission as Permission;
	
class Add_Permissions_To_Admin_User {

	private $admin_user = null;

	private $permission = null;

	public function __construct()
	{
		Config::set('verify::verify.prefix', 'usersys');

		$this->admin_user = Role::where_name('Admin')->first();
		$this->permission = Permission::where_name('make_programme_live')->first(array('id'));
	}

	/**
	 * Add the additional 'make_programme_live' permission to the admin user.
	 *
	 * @return void
	 */
	public function up()
	{
		$this->admin_user->permissions()->attach($this->permission->id);
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		$this->admin_user->permissions()->detach($this->permission->id);
	}

}
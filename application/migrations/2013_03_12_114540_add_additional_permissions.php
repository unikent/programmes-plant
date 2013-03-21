<?php

use \Verify\Models\Role as Role;
use \Verify\Models\Permission as Permission;

class Add_Additional_Permissions {

	private $permissions = array(
		'view_rollback_options',
		'unpublish_programmes',
		'view_revisions',
		'revert_revisions'
	);

	public function __construct()
	{
		Config::set('verify::verify.prefix', 'usersys');
	}

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		$admin_user = Role::where('name', '=', 'Admin')->first();

		foreach($this->permissions as $permission)
		{	
			$p = new Permission(array('name' => $permission));
			$p->save();

			$admin_user->permissions()->attach($p);
		}
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		$admin_user = Role::where('name', '=', 'Admin')->first();

		foreach($this->permissions as $permission)
		{
			$p = Permission::where('name', '=', $permission)->first();

			$admin_user->permissions()->detach($p);

			$p->delete();
		}
	}

}
<?php
require_once Bundle::path('verify_ldap') . '../verify/models/EloquentVerifyBase.php';
require_once Bundle::path('verify_ldap') . '../verify/models/Role.php';
require_once Bundle::path('verify_ldap') . '../verify/models/Permission.php';

Config::set('verify::verify.prefix', 'usersys');

use \Verify\Models\Permission, \Verify\Models\Role ;


class setup_perms_and_groups {

	
	private $permissions = array(
		'manage_users',
		'configure_fields',
		'view_all_programmes',
		'edit_own_programmes',
		'edit_all_programmes',
		'create_programmes',
		'publish',
		'view_revisions',
		'edit_data',
		'edit_immutable_data',
		'edit_overridable_data',
	);


	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		$perms = array();

		foreach($this->permissions as $permission_name){
			$permission = new Permission;
			$permission->name = $permission_name;
			$permission->save();

			$perms[$permission->name] = $permission->id;
		}

		$admin = new Role;
		$admin->name = 'Admin';
		$admin->level = 9;
		$admin->save();
		$admin->permissions()->sync(array_values($perms));

		$user = new Role;
		$user->name = 'User';
		$user->level = 5;
		$user->save();
		$user->permissions()->sync(array($perms['edit_own_programmes']));
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		// These tables get droped entirely by the next migration back
	}

}
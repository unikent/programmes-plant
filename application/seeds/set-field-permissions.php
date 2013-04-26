<?php
Config::set('verify::verify.prefix', 'usersys');

use \Verify\Models\Permission;

class Set_Field_Permissions {

	
	
	/**
	 * 
	 */
	function run()
	{
		$this->add_all_permissions_to_admin_and_user_roles();
	}

	/**
	 * Assigns all permissions in the systems to to users and admins
	 */
	function add_all_permissions_to_admin_and_user_roles(){
		$permissions = Permission::all(); //DB::table('usersys_permissions')->get();
		
		foreach ($permissions as $permission) {
			$permission->roles()->sync(Role::sanitize_ids(array(2,3)));
		}
	}

	
}
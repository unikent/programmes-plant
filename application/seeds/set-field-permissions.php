<?php
Config::set('verify::verify.prefix', 'usersys');

use \Verify\Models\Permission;

class Set_Field_Permissions {

	/**
	 * Run the permissions seed. Here's how
	 * php artisan seed set-field-permissions --env=[environment]
	 */
	function run()
	{
		$fields = UG_ProgrammeField::all();
		$this->add_all_permissions_to_admin_and_user_roles();
		$this->add_specified_roles($fields);
	}

	/**
	 * Assigns all permissions in the systems to users and admins
	 */
	function add_all_permissions_to_admin_and_user_roles(){
		$permissions = Permission::all();
		
		foreach ($permissions as $permission) {
			$permission->roles()->sync(array(2,3));
		}
	}

	/**
	 * Assigns all permissions in the systems to admins, and only the specified ones to users
	 */
	function add_specified_roles($fields){
		foreach ($fields as $field) {
			$read_permission = Permission::where_name('ug_fields_read_' . $field->colname)->first();
			$write_permission = Permission::where_name('ug_fields_write_' . $field->colname)->first();
			
			$user_can_read = true;
			$user_can_write = true;

			$read_roles = $user_can_read ? array(2,3) : array(2);
			$write_roles = $user_can_write ? array(2,3) : array(2);

			if ($user_can_read) echo "adding user to fields_read_{$field->colname} permission\n";
			
			$read_permission->roles()->sync($read_roles);

			if ($user_can_write) echo "adding user to fields_write_{$field->colname} permission\n";
			$write_permission->roles()->sync($write_roles);
		}
	}
	
}
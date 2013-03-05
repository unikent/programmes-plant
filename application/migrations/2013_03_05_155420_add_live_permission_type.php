<?php
require_once Bundle::path('verify_ldap') . '../verify/models/EloquentVerifyBase.php';
require_once Bundle::path('verify_ldap') . '../verify/models/Permission.php';

use \Verify\Models\Permission as Permission;

class Add_Live_Permission_Type {

	private $permissions = array(
		'make_programme_live',
		'submit_programme_for_editing'
	);

	public function __construct()
	{
		Config::set('verify::verify.prefix', 'usersys');
	}

	/**
	 * Add additional permission types.
	 *
	 * @return void
	 */
	public function up()
	{
		foreach($this->permissions as $permission_name)
		{
			$permission = Permission::create(array('name' => $permission_name));
			$permission->save();
		}
	}

	/**
	 * Remove additional permission types.
	 *
	 * @return void
	 */
	public function down()
	{
		foreach($this->permissions as $permission_name)
		{
			$permission = Permission::where('name', '=', $permission_name)->first();
			$permission->delete();
		}
	}

}
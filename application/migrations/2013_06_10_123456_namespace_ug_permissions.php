<?php
Config::set('verify::verify.prefix', 'usersys');

class Namespace_Ug_Permissions {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		foreach(DB::table('usersys_permissions')->get() as $perm){
			if((strpos($perm->name, 'fields_') === 0) || (strpos($perm->name, 'sections_') === 0)){
				// Update each perm to include
				DB::table('usersys_permissions')->where('id','=', $perm->id)->update(array('name' => 'ug_'.$perm->name));
			}
		}
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		foreach(DB::table('usersys_permissions')->get() as $perm){
			if((strpos($perm->name, 'ug_fields_') === 0) || (strpos($perm->name, 'ug_sections_') === 0)){
				// Update each perm to include
				DB::table('usersys_permissions')->where('id','=', $perm->id)->update(array('name' => str_replace('ug_','',$perm->name)));
			}
		}
	}
}
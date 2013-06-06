<?php
Config::set('verify::verify.prefix', 'usersys');

class Add_Section_Permissions_Pg {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		$sections = PG_ProgrammeSection::get();
		foreach($sections as $section){
			$name = $section->get_slug();

			$permission = new Permission;
			$permission->name = "sections_autoexpand_{$name}";
			$permission->save();
		}
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		$sections = PG_ProgrammeSection::get();
		foreach($sections as $section){
			$name = $section->get_slug();

			$permissions = Permission::where('name', '=', "sections_autoexpand_{$name}")->get();
			foreach($permissions as $permission){
				$permission->delete();
			}
		}
	}

}
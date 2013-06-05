<?php
Config::set('verify::verify.prefix', 'usersys');

if ( ! class_exists('LegacyProgrammeSection') )
{
	class LegacyProgrammeSection extends ProgrammeSection {
		public static $table = 'programmesections';
	}
}

class Add_Section_Permissions {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		$sections = LegacyProgrammeSection::get();
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
		$sections = LegacyProgrammeSection::get();
		foreach($sections as $section){
			$name = $section->get_slug();

			$permissions = Permission::where('name', '=', "sections_autoexpand_{$name}")->get();
			foreach($permissions as $permission){
				$permission->delete();
			}
		}
	}

}

<?php
Config::set('verify::verify.prefix', 'usersys');

if ( ! class_exists('LegacyProgrammeField') )
{
	class LegacyProgrammeField extends ProgrammeField {
		public static $table = 'programmes_fields';
	}
}

class Add_Field_Permissions {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		$fields = array_merge(GlobalSettingField::all(), LegacyProgrammeField::all());
		foreach($fields as $field){
			$permission = new Permission;
			$permission->name = "fields_read_{$field->colname}";
			$permission->save();

			$permission->roles()->sync(array(2, 3)); // Grant read rights to Admin and User as default

			$permission = new Permission;
			$permission->name = "fields_write_{$field->colname}";
			$permission->save();

			$permission->roles()->sync(array(2, 3)); // Grant read rights to Admin and User as default
		}
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		$fields = array_merge(GlobalSettingField::all(), LegacyProgrammeField::all());
		foreach($fields as $field){
			$permissions = array_merge(
				Permission::where('name', '=', "fields_read_{$field->colname}")->get(),
				Permission::where('name', '=', "fields_write_{$field->colname}")->get()
			);

			foreach($permissions as $permission){
				$permission->roles()->sync(array());
				$permission->delete();
			}
		}
	}

}
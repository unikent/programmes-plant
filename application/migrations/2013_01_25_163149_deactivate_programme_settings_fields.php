<?php

class Deactivate_Programme_Settings_Fields {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		$programme_settings_fields = ProgrammeField::where('programme_field_type', '=', '1')->get();
		foreach ($programme_settings_fields as $programme_settings_field) {
			$programme_settings_field->active = false; 
			$programme_settings_field->save();
		}
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		$programme_settings_fields = ProgrammeField::where('programme_field_type', '=', '1')->get();
		foreach ($programme_settings_fields as $programme_settings_field) {
			$programme_settings_field->active = true; 
			$programme_settings_field->save();
		}
	}

}
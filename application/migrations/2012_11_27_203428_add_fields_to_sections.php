<?php

class Add_Fields_To_Sections {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		$programme_fields = ProgrammeField::programme_fields();
		foreach ($programme_fields as $programme_field)
		{
    		$programme_field->section = 1;
    		$programme_field->save();
		}
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		$programme_fields = Programme::all();

		foreach ($programme_fields as $$programme_field)
		{
			$programme_field->section = 0;
			$programme_field->save();
		}
	}

}
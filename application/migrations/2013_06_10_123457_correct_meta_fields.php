<?php

class Correct_Meta_Fields {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::table('programmes_fields_ug')->where('field_meta','=', 'Programme')->update(array('field_meta' => 'UG_Programme'));
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::table('programmes_fields_ug')->where('field_meta','=', 'UG_Programme')->update(array('field_meta' => 'Programme'));
	}
}
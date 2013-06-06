<?php

class Rename_UG_Tables {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::rename('programmes',			'programmes_ug');
		Schema::rename('programmes_revisions',	'programmes_revisions_ug');
		Schema::rename('programme_settings', 	'programme_settings_ug');
		Schema::rename('programme_settings_revisions','programme_settings_revisions_ug');
		Schema::rename('programmes_fields',		'programmes_fields_ug');
		Schema::rename('programmesections',		'programmesections_ug');
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::rename('programmes_ug',				'programmes');
		Schema::rename('programmes_revisions_ug',	'programmes_revisions' );
		Schema::rename('programme_settings_ug', 	'programme_settings');
		Schema::rename('programme_settings_revisions_ug','programme_settings_revisions');
		Schema::rename('programmes_fields_ug',		'programmes_fields');	
		Schema::rename('programmesections_ug',		'programmesections');
	}

}
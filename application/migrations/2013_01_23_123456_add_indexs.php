<?php

class Add_indexs {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{	
		Schema::table('programmes_revisions', function($table)
		{
			$table->index(array('programme_id', 'year', 'status'));
		});
		Schema::table('global_settings_revisions', function($table)
		{
			$table->index(array('year', 'status'));
		});
		Schema::table('programme_settings_revisions', function($table)
		{
			$table->index(array('year', 'status'));
		});
		Schema::table('programmes_fields', function($table)
		{
			$table->index('section');
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('programmes_revisions', function($table)
		{
			$table->drop_index('programmes_revisions_programme_id_year_status_index');
		});
		Schema::table('global_settings_revisions', function($table)
		{
			$table->drop_index('global_settings_revisions_year_status_index');
		});
		Schema::table('programme_settings_revisions', function($table)
		{
			$table->drop_index('programme_settings_revisions_year_status_index');
		});
		Schema::table('programmes_fields', function($table)
		{
			$table->drop_index('programmes_fields_section_index');
		});

	}

}
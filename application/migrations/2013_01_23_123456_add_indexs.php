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
			$table->dropIndex(array('programme_id', 'year', 'status'));
		});
		Schema::table('global_settings_revisions', function($table)
		{
			$table->dropIndex(array('year', 'status'));
		});
		Schema::table('programme_settings_revisions', function($table)
		{
			$table->dropIndex(array('year', 'status'));
		});
		Schema::table('programmes_fields', function($table)
		{
			$table->dropIndex('section');
		});

	}

}
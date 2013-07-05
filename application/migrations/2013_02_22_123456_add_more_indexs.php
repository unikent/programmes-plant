<?php

class Add_more_indexs {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{	
		Schema::table('programmes_revisions', function($table)
		{
			$table->index(array('instance_id', 'year', 'status'));
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
			$table->drop_index('programmes_revisions_instance_id_year_status_index');
		});


	}

}
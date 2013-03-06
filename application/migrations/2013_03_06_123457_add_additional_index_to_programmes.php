<?php

class Add_Additional_Index_To_Programmes {

	
	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('programmes', function($table)
		{
			$table->index(array('year', 'hidden'));
			$table->index('programme_title_1');
		});
		
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('programmes', function($table)
		{	
			// Doesnt seem to work out what it called them so have to drop by true name
			$table->drop_index('programmes_programme_title_1_index');
			$table->drop_index('programmes_year_hidden_index');
		});
	}

}
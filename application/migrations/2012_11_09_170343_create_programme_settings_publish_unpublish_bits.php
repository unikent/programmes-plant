<?php

class Create_Programme_Settings_Publish_Unpublish_Bits {

	/**
	 * Add publishing and unpublishing bits to the databases.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('programme_settings', function($table){	
			$table->integer('live');
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('programme_settings', function($table){	
			$table->drop_column('live');
		});
	}

}
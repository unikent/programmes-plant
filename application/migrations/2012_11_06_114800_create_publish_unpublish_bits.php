<?php

class Create_Publish_Unpublish_Bits {

	/**
	 * Add publishing and unpublishing bits to the databases.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('programmes', function($table){	
			$table->integer('live');
		});

		Schema::table('globalsettings', function($table){	
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
		Schema::table('programmes', function($table){	
			$table->drop_column('live');
		});

		Schema::table('globalsettings', function($table){	
			$table->drop_column('live');
		});
	}

}
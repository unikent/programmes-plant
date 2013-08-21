<?php

class Add_description_to_deliveries {

		/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('pg_programme_deliveries', function($table){	
			$table->string('description', 255);
		});

	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('pg_programme_deliveries', function($table){	
			$table->drop_column('description');
		});
	}

}
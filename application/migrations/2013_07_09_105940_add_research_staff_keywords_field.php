<?php

class Add_Research_Staff_Keywords_Field {

		/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('research_staff', function($table){	
			$table->string('keywords', 255);
		});

	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('research_staff', function($table){	
			$table->drop_column('keywords');
		});
	}

}
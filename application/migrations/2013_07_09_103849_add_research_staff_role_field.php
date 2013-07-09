<?php

class Add_Research_Staff_Role_Field {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('research_staff', function($table){	
			$table->string('role', 255);
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
			$table->drop_column('role');
		});
	}

}
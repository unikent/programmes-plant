<?php

class Add_Subjects_To_Users {

	
	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('usersys_users', function($table)
		{
			$table->string('subjects', 255);
		});
		
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('usersys_users', function($table)
		{
			$table->drop_column('subjects');
		});
	}

}
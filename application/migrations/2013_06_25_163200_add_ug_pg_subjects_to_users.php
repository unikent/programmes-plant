<?php

class Add_Ug_Pg_Subjects_To_Users {

	
	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('usersys_users', function($table)
		{
			$table->string('pg_subjects', 255);
			$table->rename_column('subjects','ug_subjects');
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
			$table->drop_column('pg_subjects');
			$table->rename_column('ug_subjects','subjects');
		});
	}

}
<?php

class Add_Ug_Pg_Subjects_To_Users {

	
	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Request::env() != 'test'){
			DB::query("ALTER TABLE usersys_users CHANGE subjects ug_subjects varchar(255)");
			DB::query("ALTER TABLE usersys_users ADD COLUMN pg_subjects varchar(255)");
		}
		else{
			Schema::table('usersys_users', function($table)
			{
				$table->string('pg_subjects', 255);
				$table->string('ug_subjects', 255);
			
			});
		}
		
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		if (Request::env() != 'test'){
			DB::query("ALTER TABLE usersys_users CHANGE ug_subjects subjects varchar(255)");
			DB::query("ALTER TABLE usersys_users DROP COLUMN pg_subjects");
		}
		else{
			Schema::table('usersys_users', function($table)
			{
				$table->drop_column('pg_subjects');
				$table->drop_column('ug_subjects');
			});
		}
	}

}
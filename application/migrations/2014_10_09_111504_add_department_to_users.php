<?php

class Add_Department_To_Users {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('usersys_users', function($table)
		{
			$table->string('department', 255)->nullable();
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
			$table->drop_column('department');
		});
	}

}
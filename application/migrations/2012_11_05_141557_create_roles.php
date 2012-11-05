<?php

class Create_Roles {

	/**
	 * Creates the roles table, that sets the permissions for each user.
	 *
	 * @return void
	 */
	public function up()
	{
		// Create the roles table
		Schema::create('roles', function($table){
			$table->increments('id');
			$table->string('username');
			$table->string('fullname');
			$table->boolean('isadmin');
			$table->boolean('isuser');
			$table->string('department');
			$table->timestamps();
			$table->index('username');
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('roles');
	}

}
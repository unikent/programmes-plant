<?php

class hyper_administrator {

	
	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::table('usersys_roles')->where('id', '=', '1')->update(array(
			'name'			=> 'Hyper Administrator', // super admin, super user, root, the big cheese, etc
		));
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		// These tables get droped entirely by the next migration back
	}

}
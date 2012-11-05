<?php

class Create_Campuses {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('campuses', function($table){
			$table->increments('id');
			$table->timestamps();
			$table->string('name', 200);
		})
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('campuses');
	}

}
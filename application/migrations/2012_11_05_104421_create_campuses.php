<?php

class Create_Campuses {

	/**
	 * Create the campuses table.
	 * 
	 * This table stores our campuses.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('campuses', function($table){
			$table->increments('id');
			$table->timestamps();
			$table->string('name');
		});
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
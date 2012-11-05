<?php

class Create_Faculties {

	/**
	 * Create the faculties table.
	 * 
	 * This table stores the faculties.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('faculties', function($table){
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
		Schema::drop('faculties');
	}

}
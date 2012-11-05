<?php

class Create_Schools {

	/**
	 * Create the schools table.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('schools', function($table){
    		$table->increments('id');
    		$table->timestamps();
    		$table->string("name");
    		$table->integer("faculties_id");
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('schools');
	}

}
<?php

class Add_Subjects_In {

	/**
	 * Add subjects to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('subjects', function($table)
		{
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
		Schema::drop('subjects');
	}

}
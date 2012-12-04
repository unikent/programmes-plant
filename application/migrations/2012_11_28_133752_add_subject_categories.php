<?php

class Add_Subject_Categories {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('subjectcategories', function($table)
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
		Schema::drop('subjectcategories');
	}

}
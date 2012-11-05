<?php

class Create_Awards_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('awards', function($table){
			$table->increments('id');
			$table->timestamps();
			$table->string('name', 200);
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('awards');
	}

}
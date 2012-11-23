<?php

class Create_Sections_Table {


	/**
	 * Create the awards table.
	 * 
	 * This table stores programme field sections.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('programmesections', function($table){
			$table->increments('id');
			$table->timestamps();
			$table->string('name', 200);
			$table->integer('order');
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('programmesections');
	}

}
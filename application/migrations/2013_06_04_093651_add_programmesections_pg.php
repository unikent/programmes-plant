<?php

class Add_Programmesections_Pg {

	/**
	 * Create the programmesections_pg table.
	 * 
	 * This table stores programme sections for PG
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('programmesections_pg', function($table){
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
		Schema::drop('programmesections_pg');
	}

}
<?php

class Add_Awards_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('awards', function($table){
			$table->create();
    		$table->increments('id');
    		$table->timestamps();
    		$table->string("name");
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
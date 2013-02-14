<?php

class Remove_Index_Timestamp_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::drop('api_index_time');
		
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::create('api_index_time', function($table){
			$table->increments('id');
			$table->string('level');
			$table->string('year');
			$table->timestamps();
		});
	}

}
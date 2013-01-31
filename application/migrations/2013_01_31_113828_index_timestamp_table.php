<?php

class Index_Timestamp_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('api_index_time', function($table){
			$table->string('level');
			$table->string('year');
			$table->timestamps();
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('api_index_time');
	}

}
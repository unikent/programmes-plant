<?php

class Image_Data_Type_Update {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('images', function($table){
			$table->string('title', 255);
			$table->string('alt', 255);
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('images', function($table){
			$table->drop_column('title');
			$table->drop_column('alt');
		});
	}

}
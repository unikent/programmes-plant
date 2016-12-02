<?php

class Image_Data_Add_Size {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('images', function($table){
			$table->string('height');
			$table->string('width');
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
			$table->drop_column('height');
			$table->drop_column('width');
		});
	}

}
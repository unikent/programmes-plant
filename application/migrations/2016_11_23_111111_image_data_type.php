<?php

class Image_Data_Type {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{

		Schema::create('images', function($table){
			$table->increments('id');
			$table->timestamps();

			$table->string('name', 200);
			$table->string('file_name', 255);
			$table->string('focus', 50);

			$table->string('caption', 255);
			$table->string('attribution_text', 255);
			$table->string('attribution_link', 255);
						
			$table->string('licence_link', 255);
		});

	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('images');
	}

}
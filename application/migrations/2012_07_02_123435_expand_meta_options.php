<?php

class Expand_Meta_Options {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
	
		
		Schema::table('subjects_meta', function($table){
			$table->integer("prefill")->default('0');
			$table->text("placeholder");
		});
		Schema::table('programmes_meta', function($table){
			$table->integer("prefill")->default('0');
			$table->text("placeholder");
		});



	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{	

		Schema::table('subjects_meta', function($table){
			$table->drop_column("prefill");
			$table->drop_column("placeholder");
		});
		Schema::table('programmes_meta', function($table){
			$table->drop_column("prefill");
			$table->drop_column("placeholder");
		});

	}

}
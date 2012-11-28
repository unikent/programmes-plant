<?php

class Add_Award_Long_Field {

	/**
	 * Add the award longname field
	 * 
	 *
	 * @return void
	 */
	public function up()
	{	
		Schema::table('awards', function($table){
			$table->string('longname');
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('awards', function($table){
			$table->drop_column('longname');
		});
	}

}
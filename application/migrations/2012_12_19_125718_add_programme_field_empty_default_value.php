<?php

class Add_Programme_Field_Empty_Default_Value {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
	    Schema::table('programmes_fields', function($table){
			$table->integer('empty_default_value');
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
    	Schema::table('programmes_fields', function($table){
			$table->drop_column('empty_default_value');
		});
	}

}
<?php

class Add_Programme_Fields_Order {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('programmes_fields', function($table){	
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
		Schema::table('programmes_fields', function($table){	
			$table->drop_column('order');
		});
	}

}

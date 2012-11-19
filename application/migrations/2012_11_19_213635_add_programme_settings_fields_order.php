<?php

class Add_Programme_Settings_Fields_Order {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('programme_settings_fields', function($table){	
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
		Schema::table('programme_settings_fields', function($table){	
			$table->drop_column('order');
		});
	}

}
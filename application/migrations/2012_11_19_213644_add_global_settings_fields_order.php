<?php

class Add_Global_Settings_Fields_Order {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('global_settings_fields', function($table){	
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
		Schema::table('global_settings_fields', function($table){	
			$table->drop_column('order');
		});
	}

}
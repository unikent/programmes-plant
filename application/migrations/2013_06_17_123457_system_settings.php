<?php

class System_Settings {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('system_settings', function($table){
			$table->increments('id');
			$table->timestamps();
			$table->string('ug_current_year', 200);
			$table->string('pg_current_year', 200);
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
		Schema::drop('system_settings');
	}
}
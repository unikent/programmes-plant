<?php

// Apply retroactivly using: UPDATE `programmes` SET programme_id = id;
class Add_Programme_id {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('programmes', function($table){
			$table->integer('programme_id');
			$table->index('programme_id');
		});
		Schema::table('global_settings', function($table){
			$table->integer('global_setting_id');
			$table->index('global_setting_id');
		});
		Schema::table('programme_settings', function($table){
			$table->integer('programme_setting_id');
			$table->index('programme_setting_id');
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('programmes', function($table){
			//$table->drop_index('programme_id');
			$table->drop_column('programme_id');
		});
		Schema::table('global_settings', function($table){
			//$table->drop_index('global_settings_id');
			$table->drop_column('global_setting_id');
		});
		Schema::table('programme_settings', function($table){
			//$table->drop_index('programme_settings_id');
			$table->drop_column('programme_setting_id');
		});
	}

}
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
			$table->integer('instance_id');
			$table->index('instance_id');
		});
		Schema::table('global_settings', function($table){
			$table->integer('instance_id');
			$table->index('instance_id');
		});
		Schema::table('programme_settings', function($table){
			$table->integer('instance_id');
			$table->index('instance_id');
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
			$table->drop_column('instance_id');
		});
		Schema::table('global_settings', function($table){
			$table->drop_column('instance_id');
		});
		Schema::table('programme_settings', function($table){
			$table->drop_column('instance_id');
		});
	}

}
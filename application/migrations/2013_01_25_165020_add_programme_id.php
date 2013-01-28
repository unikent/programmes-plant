<?php

// Apply retroactivly using: 
// UPDATE `programmes` SET instance_id = id;
// UPDATE `programmes_revisions` SET instance_id = programme_id;
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
		Schema::table('programmes_revisions', function($table){
			$table->integer('instance_id');
		});
		Schema::table('programme_settings_revisions', function($table){
			$table->integer('instance_id');
		});
		Schema::table('global_settings_revisions', function($table){
			$table->integer('instance_id');
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
		Schema::table('programmes_revisions', function($table){
			$table->drop_column('instance_id');
		});
		Schema::table('programme_settings_revisions', function($table){
			$table->drop_column('instance_id');
		});
		Schema::table('global_settings_revisions', function($table){
			$table->drop_column('instance_id');
		});
	}

}
<?php

class Remove_Live_Field_From_Programmes {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('programmes_ug', function($table){
			$table->drop_column('live');
		});
		Schema::table('programmes_pg', function($table){
			$table->drop_column('live');
		});
		Schema::table('programme_settings_ug', function($table){
			$table->drop_column('live');
		});
		Schema::table('programme_settings_pg', function($table){
			$table->drop_column('live');
		});
		Schema::table('global_settings', function($table){
			$table->drop_column('live');
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('programmes_ug', function($table){
			$table->integer('live');
		});
		Schema::table('programmes_pg', function($table){
			$table->integer('live');
		});
		Schema::table('programme_settings_ug', function($table){
			$table->integer('live');
		});
		Schema::table('programme_settings_pg', function($table){
			$table->integer('live');
		});
		Schema::table('global_settings', function($table){
			$table->integer('live');
		});
	}

}
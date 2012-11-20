<?php

class Create_ProgrammeSettings {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		// Create the programme_settings table
		Schema::table('programme_settings', function($table){
			$table->create();
			$table->increments('id');
			$table->string('year', 4);
			$table->string('created_by', 10);
			$table->string('published_by', 10);
			$table->timestamps();
		});

		// Create the programme_settings_revisions table
		Schema::table('programme_settings_revisions', function($table){
			$table->create();
			$table->increments('id');
			$table->integer('programme_setting_id');
			$table->string('year', 4);
			$table->string('created_by', 10);
			$table->string('status', 15);
			$table->timestamps();
		});

		// Create the programme_settings_fields table
		Schema::table('programme_settings_fields', function($table){
			$table->create();
    		$table->increments('id');
    		$table->string('field_name');
    		$table->string('field_type');
    		$table->string('field_meta');
    		$table->string('field_description');
    		$table->string('field_initval');
    		$table->integer('prefill')->default('0');
			$table->text('placeholder');
    		$table->integer('active');
    		$table->integer('view');
    		$table->string('colname');
    		$table->timestamps();
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('programme_settings');
		Schema::drop('programme_settings_fields');
		Schema::drop('programme_settings_revisions');
	}
}
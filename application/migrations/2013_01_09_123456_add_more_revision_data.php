<?php

class Add_more_revision_data {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{

	    Schema::table('programmes', function($table){
			$table->drop_column('published_by');
		});
		Schema::table('programme_settings', function($table){
			$table->drop_column('published_by');
		});
		Schema::table('global_settings', function($table){
			$table->drop_column('published_by');
		});

		Schema::table('programmes_revisions', function($table){
			$table->drop_column('created_by');
			$table->drop_column('published_by');
		});
		Schema::table('programme_settings_revisions', function($table){
			$table->drop_column('created_by');
		});
		Schema::table('global_settings_revisions', function($table){
			$table->drop_column('created_by');
		});


		Schema::table('programmes_revisions', function($table){
			$table->string('edits_by');
			$table->string('made_live_by');
			$table->date('published_at');
		});
		
		Schema::table('programme_settings_revisions', function($table){
			$table->string('edits_by');
			$table->string('made_live_by');
			$table->date('published_at');
		});
		
		Schema::table('global_settings_revisions', function($table){
			$table->string('edits_by');
			$table->string('made_live_by');
			$table->date('published_at');
		});


		
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{

		Schema::table('programmes_revisions', function($table){
	
			$table->drop_column('edits_by');
			$table->drop_column('made_live_by');
			$table->drop_column('published_at');
		
		});
		Schema::table('programme_settings_revisions', function($table){

			$table->drop_column('edits_by');
			$table->drop_column('made_live_by');
			$table->drop_column('published_at');
		});
		Schema::table('global_settings_revisions', function($table){

			$table->drop_column('edits_by');
			$table->drop_column('made_live_by');
			$table->drop_column('published_at');
		});


    	Schema::table('programmes', function($table){
			$table->string('published_by');
		});
		Schema::table('programme_settings', function($table){
			$table->string('published_by');
		});
		Schema::table('global_settings', function($table){
			$table->string('published_by');
		});

		Schema::table('programmes_revisions', function($table){
			$table->string('created_by');
			$table->integer('published_by');
		
		});
		Schema::table('programme_settings_revisions', function($table){
			$table->string('created_by');
		});
		Schema::table('global_settings_revisions', function($table){
			$table->string('created_by');
		});

	}

}
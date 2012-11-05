<?php

class Create_Programmes {

	/**
	 * Create the programmes tables.
	 * 
	 * These store the information about programmes.
	 * 
	 * There are three tables.
	 * 
	 * First the programmes table stores the current revision of the programmes.
	 * 
	 * Second the programmes_meta stores addition field and items. This is occasionally polled in order to produce new columns for the programmes table. This can therefore be an arbitary data store.
	 * 
	 * Third the programmes_revisions table. This stores revisions of the programmes table to allow rolling back and forward. It also stores all meta fields, even when they are removed from activity on the front end. 
	 *
	 * @return void
	 */
	public function up()
	{
		// Add programmes
		Schema::create('programmes', function($table){
    		$table->increments('id');
    		$table->timestamps();
    		$table->string('year', 4);
			$table->string('created_by', 10);

    		// Main
    		$table->string('title', 255);
    		$table->string('slug');
    		$table->string('honours');
    		$table->text('summary');

    		// Relations
    		$table->integer('school_id');
    		$table->integer('school_adm_id');
    		$table->integer('campus_id');

			$table->index('id');
			$table->index('year');

		});


		// Add programmes revisions
		Schema::create('programmes_revisions', function($table){
    		$table->increments('id');
    		$table->timestamps();
    		$table->string('year', 4);
			$table->string('created_by', 10);

    		// Main
    		$table->string('title', 255);
    		$table->string('slug');
    		$table->string('honours');
    		$table->text('summary');

    		// Relations
    		$table->integer('school_id');
    		$table->integer('school_adm_id');
    		$table->integer('campus_id');

			$table->index('id');
			$table->index('year');

		});

		// Add programmes_meta
		Schema::create('programmes_meta', function($table){
			$table->increments('id');

			$table->string('field_name');
			$table->string('field_type');
			$table->string('field_meta');
			$table->string('field_description');
			$table->string('field_initval');

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
		Schema::drop('programmes');
		Schema::drop('programmes_revisions');
		Schema::drop('programmes_meta');
	}

}
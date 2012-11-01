<?php

class Add_Meta {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		//Add meta tables
		Schema::table('subjects_meta', function($table){
			$table->create();
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
		Schema::table('programmes_meta', function($table){
			$table->create();
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
		//Update subject to store all needed feilds.
		Schema::table('subjects_revisions', function($table){
			$table->drop_column('factbox');
			$table->integer('school_id');
		});
		Schema::table('subjects', function($table){
			$table->drop_column('factbox');
			$table->integer('school_id');
		});
		//Add programmes
		Schema::table('programmes', function($table){

			$table->create();
    		$table->increments('id');
    		$table->timestamps();
    		$table->string('year',4);
			$table->string('created_by',10);

    		//Main
    		$table->string('title',255);
    		$table->string('slug');
    		$table->string('honours');
    		$table->text('summary');

    		//Relations
    		$table->integer('school_id');
    		$table->integer('school_adm_id');
    		$table->integer('campus_id');
    		$table->integer('subject_id');

			$table->index('id');
			$table->index('year');

		});
		//Add programmes revisions
		Schema::table('programmes_revisions', function($table){

			$table->create();
    		$table->increments('id');
    		$table->timestamps();
    		$table->string('year',4);
			$table->string('created_by',10);

    		//Main
    		$table->string('title',255);
    		$table->string('slug');
    		$table->string('honours');
    		$table->text('summary');

    		//Relations
    		$table->integer('school_id');
    		$table->integer('school_adm_id');
    		$table->integer('campus_id');
    		$table->integer('subject_id');

			$table->index('id');
			$table->index('year');

		});


		

	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{	
		Schema::drop('subjects_meta');
		Schema::drop('programmes_meta');
		Schema::drop('programmes');
		Schema::drop('programmes_revisions');
	}

}
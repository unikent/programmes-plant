<?php

class Add_Supersubjects_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		// Create the supersubjects tables
		Schema::table('supersubjects', function($table){
			$table->create();
    		$table->increments('id');

    		$table->string("title");
    		$table->string('year',4);
			$table->string('created_by',10);
			$table->string('published_by',10);
			$table->string('subject_ids');
			$table->string('programme_ids');
    		$table->timestamps();

    		$table->index('id');
    		$table->index('year');
		});

		// Create the supersubject revisions tables
		Schema::table('supersubjects_revisions', function($table){
			$table->create();
			$table->increments('id');
			$table->integer('supersubject_id');

			$table->string('title',255);
			$table->string('year',4);
			$table->string('created_by',10);
			$table->string('subject_ids');
			$table->string('programme_ids');
			$table->string('status', 15)->nullable();
			$table->timestamps();

			$table->index('id');
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{	

		Schema::drop('supersubjects');
		Schema::drop('supersubjects_revisions');

	}

}
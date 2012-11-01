<?php

class Add_Subject_School{

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
	
		// Create the subjects table
		Schema::table('subjects', function($table){	
			$table->string('secondary_school_ids',255);
			$table->string('related_subject_ids',255);
			$table->integer('main_school_id');
		});
		Schema::table('subjects_revisions', function($table){
			$table->string('secondary_school_ids',255);
			$table->string('related_subject_ids',255);
			$table->integer('main_school_id');
			
		});


	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down(){


	}

	
}
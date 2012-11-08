<?php

class Create_Related {

	/**
	 * Create Related Fields In The Database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('programmes', function($table){	
			$table->string('related_school_ids',255);
			$table->string('related_subject_ids',255);
			$table->string('related_programme_ids',255);
		});

		Schema::table('programme_revisions', function($table){
			$table->string('related_school_ids',255);
			$table->string('related_subject_ids',255);
			$table->string('related_programme_ids',255);
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
			$table->drop_column(array(
				'related_school_ids',
				'related_subject_ids',
				'related_programme_ids'
			));
		});

		Schema::table('programme_revisions', function($table){
			$table->drop_column(array(
				'related_school_ids',
				'related_subject_ids',
				'related_programme_ids'
			));
		});
	}

}
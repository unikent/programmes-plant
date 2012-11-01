<?php

class Add_Audit_Trail {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('subjects', function($table){
			$table->integer('published_by')->nullable();
		});

		Schema::table('subjects_revisions', function($table){
			$table->string('status', 15)->nullable();
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		
	}

}
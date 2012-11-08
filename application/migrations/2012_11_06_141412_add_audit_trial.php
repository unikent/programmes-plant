<?php

class Add_Audit_Trial {

	/**
	 * Add audit trail to revisions.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('programmes', function($table){
			$table->string('published_by');
		});

		Schema::table('programme_revisions', function($table){
			$table->integer("published_by");
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
			$table->drop_column('published_by');
		});

		Schema::table('programme_revisions', function($table){
			$table->drop_column("published_by");
 		});
	}

}
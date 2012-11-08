<?php

class Add_Status_And_Programme_Id {

	/**
	 * Add additional columns to the programmes revisions table.
	 * 
	 * @return void
	 */
	public function up()
	{
		Schema::table('programmes_revisions', function($table){
			$table->string("status");
			$table->integer("programme_id");
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
			$table->drop_column("status");
			$table->drop_column("programme_id");
		});
	}

}
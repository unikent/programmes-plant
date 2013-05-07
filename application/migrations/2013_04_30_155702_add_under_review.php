<?php

class Add_Under_Review {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('programmes_revisions', function($table){
			$table->integer('under_review');
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
			$table->drop_column('under_review');
		});
	}

}
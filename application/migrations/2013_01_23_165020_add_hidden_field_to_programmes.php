<?php

class Add_Hidden_Field_To_Programmes {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('programmes', function($table){
			$table->boolean('hidden');
		});

		Schema::table('programmes_revisions', function($table){
			$table->boolean('hidden');
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
			$table->drop_column('hidden');
		});

		Schema::table('programmes_revisions', function($table){
			$table->drop_column('hidden');
		});
	}

}
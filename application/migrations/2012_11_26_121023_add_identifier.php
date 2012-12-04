<?php

class Add_Identifier {

	/**
	 * Add identifier to campuses.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('campuses', function($table){
			$table->integer('identifier');
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('campuses', function($table){
			$table->drop_column('identifier');
		});
	}

}
<?php

class Add_Programme_id {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('programmes', function($table){
			$table->integer('programme_id');
			$table->index('programme_id');
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
			$table->drop_column('programme_id');
			$table->drop_index('programme_id');
		});
	}

}
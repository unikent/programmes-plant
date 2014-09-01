<?php

class Create_Ipo_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('pg_programme_deliveries', function($table){
			$table->string('current_ipo', 4);
			$table->string('previous_ipo', 4);
		});

		Schema::table('programmes_ug', function($table){
			$table->string('current_ipo_pt', 4);
			$table->string('previous_ipo_pt', 4);
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('pg_programme_deliveries', function($table){
			$table->drop_column('current_ipo');
			$table->drop_column('previous_ipo');
		});
		Schema::table('programmes_ug', function($table){
			$table->drop_column('current_ipo_pt');
			$table->drop_column('previous_ipo_pt');
		});
	}

}
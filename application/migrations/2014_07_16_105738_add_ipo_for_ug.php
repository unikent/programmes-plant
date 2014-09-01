<?php

class Add_Ipo_For_Ug {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('programmes_revisions_ug', function($table){
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
		Schema::table('programmes_revisions_ug', function($table){
			$table->drop_column('current_ipo_pt');
			$table->drop_column('previous_ipo_pt');
		});
	}

}
<?php

class Add_Ari_For_Ug {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('programmes_revisions_ug', function($table){
			$table->string('ari_code', 12);
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
			$table->drop_column('ari_code');
		});
	}

}
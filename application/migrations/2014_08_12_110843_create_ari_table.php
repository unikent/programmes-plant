<?php

class Create_ARI_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('pg_programme_deliveries', function($table){
			$table->string('ari_code', 12);

		});

		Schema::table('programmes_ug', function($table){
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
		Schema::table('pg_programme_deliveries', function($table){
			$table->drop_column('ari_code');
		});
		Schema::table('programmes_ug', function($table){
			$table->drop_column('ari_code');
		});
	}

}
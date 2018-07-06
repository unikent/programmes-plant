<?php

class Add_Crs_Code_To_Deliveries_For_Sits_Integrations {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('ug_programme_deliveries', function($table) {
			$table->string('crs_code',50)->nullable();
		});
		Schema::table('pg_programme_deliveries', function($table) {
			$table->string('crs_code',50)->nullable();
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('ug_programme_deliveries', function($table) {
			$table->dropColumn('crs_code');
		});
		Schema::table('pg_programme_deliveries', function($table) {
			$table->dropColumn('crs_code');
		});	}

}
<?php

class Add_Crs_Field_To_Deliveries {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{		
		Schema::table('pg_programme_deliveries', function($table){
			$table->string('crs', 255)->nullable();
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
			$table->dropColumn('crs');
		});	
	}

}
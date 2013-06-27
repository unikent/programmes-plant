<?php

class Add_Campus_Data {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		// Add campus demo data
		foreach (array('Canterbury', 'Medway', 'Paris', 'Brussels', 'Tonbridge') as $campus)
		{
	        DB::table('campuses')->insert(array('name'=> $campus));
		}
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::table('campuses')->where('id','=','*')->delete();
	}

}
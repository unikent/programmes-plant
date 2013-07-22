<?php

class Add_School_Data {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
    	// Add school demo data
		foreach (array('Architecture', 'Arts', 'English', 'European Culture and Languages', 'History') as $school)
		{
	        DB::table('schools')->insert(array('name'=> $school, 'faculties_id' => 1));
		}
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::table('schools')->where('id','=','*')->delete();
	}

}
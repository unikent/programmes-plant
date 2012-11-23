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
			$obj = new School;
			$obj->name = $school;
			$obj->faculties_id = 1;
			$obj->save();
		}
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
    	$schools = School::all();
		foreach ($schools as $school)
		{
			$school->delete();
		}
	}

}
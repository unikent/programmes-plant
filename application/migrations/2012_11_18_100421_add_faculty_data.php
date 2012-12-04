<?php

class Add_Faculty_Data {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		// Add faculty demo data
		foreach (array('Humanities', 'Sciences', 'Social Sciences') as $faculty)
		{
			$obj = new Faculty;
			$obj->name = $faculty;
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
    	$faculties = Faculty::all();
		foreach ($faculties as $faculty)
		{
			$faculty->delete();
		}
	}

}
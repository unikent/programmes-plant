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
			$obj = new Campus;
			$obj->name = $campus;
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
    	$campuses = Campus::all();
		foreach ($campuses as $campus)
		{
			$campus->delete();
		}
	}

}
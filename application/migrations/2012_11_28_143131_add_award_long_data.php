<?php

class Add_Award_Long_Data {

	/**
	 * Adds the award long field data
	 *
	 * @return void
	 */
	public function up()
	{
		// Add award demo data
		foreach (array('Bachelor of Science with Honours', 'Bachelor of Arts with Honours', 'Master of Science', 'Master of Arts') as $key=>$value)
		{
    		$id = ($key+1);
			$tmp = Award::find($id);
			$tmp->longname = $value;
			$tmp->save();
		}
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		$awards = Award::all();

		foreach ($awards as $award)
		{
			$award->longname = '';
			$award->save();
		}
	}

}
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
    		DB::table("awards")->where('id','=',$key+1)->update(
				array(
					'longname'=> $value
				 )
			);
		}
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		// This column gets dropped in the next migration back + will be overwritten by this one
	}

}
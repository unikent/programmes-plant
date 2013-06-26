<?php

class Create_Initial_Awards {

	/**
	 * Adds some awards to allow basic saving to work.
	 *
	 * @return void
	 */
	public function up()
	{
		// Add award demo data
		foreach (array('BSc (Hons)', 'BA (Hons)', 'Msc', 'MA') as $award)
		{
			DB::table("awards")->insert(array('name'=> $award));
		}
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{	
		DB::table("awards")->where(1, '=', 1)->delete();
	}

}
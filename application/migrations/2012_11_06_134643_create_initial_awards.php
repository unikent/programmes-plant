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
			$tmp = new Award;
			$tmp->name = $award;
			$tmp->save();
			unset($tmp);
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
			$award->delete();
		}
	}

}
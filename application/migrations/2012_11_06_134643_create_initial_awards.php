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
		$tmp = new Award;

		$tmp->name = 'BSc (Hons)';
		$tmp->save();
		$tmp = new Award;
		$tmp->name = 'BA (Hons)';
		$tmp->save();
		$tmp = new Award;
		$tmp->name = 'Msc';
		$tmp->save();
		$tmp = new Award;
		$tmp->name = 'MA';
		$tmp->save();
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
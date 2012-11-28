<?php

class Add_Subject_Data {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		$subject = new Subject;
		$subject->name = 'Architecture';
		$subject->save();
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		$subjects = Subject::all();
		foreach ($subjects as $subject)
		{
			$subject->delete();
		}
	}

}
<?php

class Add_Sections_Data {

	/**
	 * Adds some sections to allow basic saving to work.
	 *
	 * @return void
	 */
	public function up()
	{
		// Add award demo data
		foreach (array('Overview', 'Teaching', 'Careers', 'Entry', 'Further info') as $count => $section)
		{
			$tmp = new ProgrammeSection;
			$tmp->name = $section;
			$tmp->order = $count;
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
		$sections = ProgrammeSection::all();

		foreach ($sections as $section)
		{
			$section->delete();
		}
	}

}
<?php

if ( ! class_exists('LegacyProgrammeSection') )
{
	class LegacyProgrammeSection extends ProgrammeSection {
		public static $table = 'programmesections';
	}
}

class Add_Sections_Data {

	/**
	 * Adds some sections to allow basic saving to work.
	 *
	 * @return void
	 */
	public function up()
	{
		// Add award demo data
		foreach (array('Programme title and key facts', 'Overview', 'Course Structure', 'Teaching and Assessment', 'Careers', 'Entry requirements ', 'Fees and Funding', 'How to apply', 'Further information', 'KIS details', 'Page administration') as $count => $section)
		{
			$tmp = new LegacyProgrammeSection;
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
		$sections = LegacyProgrammeSection::all();

		foreach ($sections as $section)
		{
			$section->delete();
		}
	}

}
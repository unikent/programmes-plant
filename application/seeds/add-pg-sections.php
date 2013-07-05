<?php

Config::set('verify::verify.prefix', 'usersys');

class Add_Pg_Sections {

	public function run()
	{
		// Add award demo data
		foreach (array('Programme title', 'Key facts', 'Course structure', 'Key information', 'Careers and employability', 'Research', 'Fees and Funding', 'How to apply', 'Further information', 'Page administration', 'Overview') as $count => $section)
		{
			$tmp = new PG_ProgrammeSection;
			$tmp->name = $section;
			$tmp->order = $count;
			$tmp->save();
		}

		$sections = PG_ProgrammeSection::get();
		foreach($sections as $section){
			$name = $section->get_slug();
			$permission = new Permission;
			$permission->name = "pg_sections_autoexpand_{$name}";
			$permission->save();
		}
	}

}
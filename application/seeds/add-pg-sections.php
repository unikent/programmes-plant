<?php

class Add_Pg_Sections {

	public function run()
	{
		// Add award demo data
		foreach (array('Programme title', 'Key facts', 'Course structure', 'Key information', 'Careers and employability', 'Research', 'Fees and Funding', 'How to apply', 'Further information', 'Page administration') as $count => $section)
		{
			$tmp = new Pg_ProgrammeSection;
			$tmp->name = $section;
			$tmp->order = $count;
			$tmp->save();
		}

		$sections = Pg_ProgrammeSection::get();
		foreach($sections as $section){
			$name = $section->get_slug();
			$permission = new Permission;
			$permission->name = "sections_autoexpand_{$name}";
			$permission->save();
		}
	}

}
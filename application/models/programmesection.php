<?php

class ProgrammeSection extends Field {
    
	/**
	 * Gives us the sections as a list, for use in a <option> tag.
	 * 
	 * @return array $options An array where the record ID maps onto the record name.
	 */
	public static function getAsList()
	{
		$sections = self::all();

		$options = array();

		foreach ($sections as $section) 
		{
			$options[$section->id] = $section->name;
		}

		return $options;
     }
}
<?php

class Subject extends Eloquent
{
	/**
	 * Gives us the subjects as a list, for use in a <option> tag.
	 * 
	 * @return array $options An array where the record ID maps onto the record name.
	 */
	public static function getAsList()
	{
		$subjects = self::all();

		$options = array();

		foreach ($subjects as $subject)
		{
			$options[$subject->id] = $subject->name;
		}

		return $options;
     }
}
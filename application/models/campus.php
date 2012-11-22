<?php
class Campus extends Eloquent {

    /**
	 * Gives us the campuses as a list, for use in a <option> tag.
	 * 
	 * @return array $options An array where the record ID maps onto the record name.
	 */
	public static function getAsList()
	{
		$campuses = self::all();

		$options = array();

		foreach ($campuses as $campus) 
		{
			$options[$campus->id] = $campus->name;
		}

		return $options;
    }

}
<?php
class Award extends Eloquent {

	/**
	 * Gives us the awards as a list, for use in a <option> tag.
	 * 
	 * @return array $options An array where the record ID maps onto the record name.
	 */
	public static function getAsList()
	{
		$awards = Award::all();

		$options = array();

		foreach ($awards as $award) 
		{
			$options[$award->id] = $award->name;
		}

		return $options;
     }

}
<?php
class SimpleData extends Eloquent
{

    /**
	 * Gives us the model types as a list, for use in a <option> tag.
	 * 
	 * @return array $options An array where the record ID maps onto the record name.
	 */
	public static function getAsList()
	{
		$data = self::all();

		$options = array();

		foreach ($data as $item) 
		{
			$options[$item->id] = $item->name;
		}

		return $options;
    }

}
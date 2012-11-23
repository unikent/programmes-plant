<?php
class SimpleData extends Eloquent
{

    /**
	 * Gives us the model types as a array, for use in a <option> tag.
	 * 
	 * @return array $options An array where the record ID maps onto the record name.
	 */
	public static function all_as_list()
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
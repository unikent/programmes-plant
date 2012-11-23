<?php
class SimpleData extends Eloquent
{

    /**
	 * Gives us all the items in this model as a flat id => name array, for use in a <option> tag.
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
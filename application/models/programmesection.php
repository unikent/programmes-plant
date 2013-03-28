<?php

class ProgrammeSection extends Eloquent {
    
	/**
	 * Gives us the sections as a list, for use in a <option> tag.
	 * 
	 * @return array $options An array where the record ID maps onto the record name.
	 */
	public static function all_as_list()
	{
		$sections = self::all();

		$sections_ordered = array();

		foreach ($sections as $section) 
		{
			$sections_ordered[$section->id] = $section->name;
		}

		return $sections_ordered;
     }

    public static function reorder($order_string)
    {
        // break up the string to get the list of ids
        $order_array = explode(",", $order_string);
        
        // loop through the array of ids and update each one in the db
        foreach ($order_array as $counter => $id)
        {
            // strip out the non-relevant part of the html id to get the actual id
            $id = str_replace('section-id-', '', $id);
            
            // pull out the appropriate entry and update it with the array index (+1)
            $item = self::find($id);
            $item->order = $counter + 1;
            $item->save();
        }
    }
    
    public function programmefields()
    {
        return $this->has_many('ProgrammeField', 'section');
    }

    public function get_slug()
    {
        return self::slugify($this->name);
    }

    public static function slugify($string)
    {
        return strtolower(str_replace(' ', '_', $string));
    }
}
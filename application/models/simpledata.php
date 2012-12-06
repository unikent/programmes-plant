<?php

class SimpleData extends Eloquent
{

	/**
	 * Validation object once it has been created.
	 */
	public static $validation = null;

	/**
	 * The rules for validation in standard Laravel validation arrays.
	 */
	public static $rules = array();

	/**
	 * A variable caching the output of all_as_list across all SimpleData's children for fast response without hitting the database.
	 * 
	 * The format is 'child' => array(id => field), e.g. 'schools' => array('1' => 'Humanities', '2' => 'Arts')
	 */
	public static $list_cache = array();

	/**
	 * Validates input for Field.
	 * 
	 * @param array $input The input in Laravel input format.
	 * @param array $rules An array of Laravel validations which will overwrite the defaults for the class.
	 * @return $validaton The Laravel validation object.
	 */
	public static function is_valid($rules = null)
	{
		if (! is_null($rules))
		{
			static::$rules = array_merge(static::$rules, $rules);
		}

		$input = Input::all();

        static::$validation = Validator::make($input, static::$rules);

        return static::$validation->passes();
	}

    /**
	 * Gives us all the items in this model as a flat id => name array, for use in a <option> tag.
	 * 
	 * @return array $options An array where the record ID maps onto the record name.
	 */
	public static function all_as_list()
	{
		// If we have cached our list then return it from cache.
		if(isset(static::$list_cache[get_called_class()])) return static::$list_cache[get_called_class()];

		$data = self::get(array('id','name'));

		$options = array();

		foreach ($data as $item) 
		{
			$options[$item->id] = $item->name;
		}

		// Save the obtained items to our in memory cache, for later faster use.
		static::$list_cache[get_called_class()] = $options;

		return $options;
    }

    public function populate_from_input()
    {
    	if (is_null(static::$validation))
    	{
    		throw new NoValidationException('No validation');
    	}

    	$input = Input::all();

    	// Remove _wysihtml5_mode entirely.
    	unset($input['_wysihtml5_mode']);

    	$this->fill($input);
    }

    public function save(){
    	$saved = parent::save();
    	if($saved){
    		static::generate_json();
    	}
    	return $saved;
    }

    private static function generate_json(){
		$cache_location = $GLOBALS['laravel_paths']['storage'].'api/';
		$cache_file = $cache_location.get_called_class().'.json';
    	$data = array();

		foreach (static::all() as $record) {
			$data[$record->id] = $record->to_array();
		}

    	// if our $cache_location isnt available, create it
	    if (!is_dir($cache_location)) 
	    {
	     	mkdir($cache_location, 0755, true);
	    }

	    file_put_contents($cache_file, json_encode($data));
    }

    /**
     * This function replaces the passed-in ids with their actual record
     */
    public static function replace_ids_with_values($ids){

    	$ds_fields = static::where_in('id', explode(',',$ids))->get();
        $values = array();
        foreach ($ds_fields as $ds_field) {
            $values[$ds_field->id] = $ds_field->to_array();
        }

        return $values;
    }
}

class NoValidationException extends \Exception {}
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

		if(isset(static::$list_cache[get_called_class()])) return static::$list_cache[get_called_class()];

		$data = self::get(array('id','name'));

		$options = array();

		foreach ($data as $item) 
		{
			$options[$item->id] = $item->name;
		}

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

}

class NoValidationException extends \Exception {}
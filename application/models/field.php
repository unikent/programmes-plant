<?php

class Field extends Eloquent 
{
	public $model = '';

	public function __construct($attributes = array(), $exists = false)
	{
		$this->model = get_called_class();
		// Pass to real constructor
		parent::__construct($attributes, $exists);
	}

	/**
	 * Validation object once it has been created.
	 */
	public static $validation = null;

	/**
	 * The rules for validation in standard Laravel validation arrays.
	 */
	public static $rules = array(
		'title'  => 'required|max:255',
		'type' => 'in:text,textarea,select,checkbox,help,table_select,table_multiselect'
	);

	/**
	 * Validates input for Field.
	 * 
	 * @param array $input The input in Laravel input format.
	 * @param array $rules An array of Laravel validations which will overwrite the defaults for the class.
	 * @return $validaton The Laravel validation object.
	 */
	public static function is_valid($input = null, $rules = null)
	{
		if (is_null($rules))
		{
			$rules = static::$rules;
		}

		if (is_null($input))
		{
			$input = Input::all();
		}

        static::$validation = Validator::make($input, $rules);

        return static::$validation->passes();
	}

	/**
	 * Extract input into model.
	 */
	public function get_input()
	{
        $this->field_name = Input::get('title');
        $this->field_type = Input::get('type');
        $this->field_description = Input::get('description');

        $this->field_meta = Input::get('options');
        $this->placeholder =  Input::get('placeholder');

        $this->field_initval =  Input::get('initval');
        $this->limit =  	Input::get('limit');
        
        $this->prefill =  ( Input::get('prefill') ==1 ) ? 1 : 0;
        
        $this->empty_default_value = ( Input::get('empty_default_value') == 1 ) ? 1 : 0;
        
	}

    /**
    * Update the order value for a given class of fields
    *
    * this is called from an ajax action in the controller, in turn from reordering fields in the ui
    *
    * @param string $order_string a comma-separated list of fields, in the order in which the user wants them
    * @param int $section The section the field is in.
    */
    public static function reorder($order_string, $section)
    {
        $order_array = explode(",", $order_string);
        
        // loop through the array of ids and update each one in the db
        foreach ($order_array as $counter => $id)
        {
            // strip out the non-relevant part of the html id to get the actual id
            $id = str_replace('field-id-', '', $id);
            
            // pull out the appropriate entry and update it with the array index (+1)
            $item = self::find($id);
            $item->order = $counter + 1;

            $item->section = str_replace('section-id-', '', $section);

            $item->save();
        }

        return true;
    }

}

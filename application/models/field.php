<?php

class Field extends Eloquent 
{
	/**
	 * Validation object once it has been created.
	 */
	public static $validation = null;

	/**
	 * The rules for validation in standard Laravel validation arrays.
	 */
	public static $rules = array(
		'title'  => 'required|max:255',
		'type' => 'in:text,textarea,select,checkbox,help'
	);

	/**
	 * Validates input for Field.
	 * 
	 * @return bool True if input is valid.
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
       
        $this->prefill =  (Input::get('prefill')==1) ? 1 : 0;
	}

}

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


    public function save() {
    	$updateSchema = false;

    	// if this is a new field, remeber to create db schema
    	if(!$this->exists) $updateSchema = true;

    	// Save record
    	$saved = parent::save();
 
 		// If save went okay, update the db schema
    	if($saved && $updateSchema){

    		$this->colname = Str::slug($this->field_name, '_').'_' . $this->id;
   			$type = URLParams::get_type();

    		// Create permissions for fields
    		$this->create_field_permissions($this->colname, $type);

    		// Update relevent table schamas
			$this->updateSchama(static::$schemas);
	    	
    		parent::save();
    	}

    	return $saved;
    }

    private function updateSchama($models){

    	// Get values for schema creation
    	$column = $this->colname;
    	$inital_value = $this->field_initval;
    	$field_type = $this->field_type;

    	// Update schema for each model
    	foreach($models as $model){

    		Schema::table($model::$table, function($table) use ($column, $inital_value, $field_type)
			{
				if ($field_type=='textarea') {
					$table->text($column);
				} else {
					$table->string($column, 255)->default($inital_value);
				}
			});
		}
    }

    private function create_field_permissions($colname, $type){
    	Permission::create(array('name' => "{$type}_fields_read_{$colname}"));
    	Permission::create(array('name' => "{$type}_fields_write_{$colname}"));
    }

    public function raw_save(){
    	parent::save();
    }




}

<?php
class ProgrammeField extends Field
{
    public static $table = 'programmes_fields';

    /**
     * Stores programme field types.
     */
    public static $types = array(
    								'NORMAL' => 0,
    								'DEFAULT' => 1,
    								'OVERRIDABLE_DEFAULT' => 2
    							);

    public static function get_types_as_list()
    {
    	$list_types = array();
    	foreach (self::$types as $key => $value) {
    		$list_types[$value] = $key;
    	}
    	return $list_types;
    }
    
    public static function programme_fields_by_section()
    {
        $sections = ProgrammeSection::with('programmefields')->order_by('order','asc')->get();

        $sections_array = array();

        foreach ($sections as $section)
        {
            foreach ($section->programmefields as $programmefield)
            {
                if ($section->id > 0)
                {
                    $sections_array[$section->name][] = $programmefield;
                }
            }
        }
        return $sections_array;
    }
    
    public static function programme_fields()
    {
        return ProgrammeField::where('active','=','1')->where_in('programme_field_type', array(ProgrammeField::$types['OVERRIDABLE_DEFAULT'], ProgrammeField::$types['NORMAL']))->order_by('order','asc')->get();
    }
    
    /**
    * assign_fields()
    *
    * loop through the programme fields, assigning the user input value to the appropriate column name
    *
    * @param object $programme_obj the programme object
    * @param array $programme_fields programme fields from db
    * @param array $input_fields user input fields from the form
    * @return object $programme_obj modified programme object
    */
    public static function assign_fields($programme_obj, $programme_fields, $input_fields)
    {
        foreach ($programme_fields as $programme_field)
        {
            $colname = $programme_field->colname;
            // make sure the field is being used (if it's in section 0 then it isn't so ignore it completely)
            if ($programme_field->section > 0)
            {
                // if the field is being used add its value to the appropriate colname in the programme object
                if (isset($input_fields[$colname])) {
                    // if the field's value is an array, convert it into a comma-separated string
                    if (is_array($input_fields[$colname]))
                    {
                        $input_fields[$colname] = implode(',', $input_fields[$colname]);
                    }
                    $programme_obj->$colname = $input_fields[$colname];
                }
            }
        }
        return $programme_obj;
    }


    /**
     * Extract input into model.
     */
    public function get_input()
    {
        parent::get_input();
        $this->programme_field_type =  Input::get('programme_field_type');
    }


    /**
     * Override Eloquent's save so that we jenerate a new json file for our API
     * 
     */
    public function save()
    {
        $saved = parent::save();

        if($saved){
            static::generate_json();
        }
        
        return $saved;
    }

    /**
     * Generate a json file that represents the records in this model
     * We're however only interested in fields that have other models as their type
     */
    private static function generate_json(){
        $cache_location = path('storage') .'api/';
        $cache_file = $cache_location . get_called_class() . '.json';
        $data = array();
        
        foreach (static::where_in('field_type', array('table_select', 'table_multiselect'))
                        ->get(array('colname', 'field_meta')) as $record) {
            $data[$record->colname] = array(
                    'colname' => $record->colname,
                    'field_meta' => $record->field_meta
                );
        }

        // if our $cache_location isnt available, create it
        if (!is_dir($cache_location)) 
        {
            mkdir($cache_location, 0755, true);
        }

        file_put_contents($cache_file, json_encode($data));
    }
}

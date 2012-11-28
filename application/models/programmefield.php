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
        $sections = ProgrammeSection::order_by('order','asc')->get();

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
    
    public static function assign_fields($programme)
    {
        // get the programme fields and loop through them, assigning the user input value to the appropriate column name
        $programme_fields = ProgrammeField::programme_fields();
        foreach ($programme_fields as $programme_field)
        {
            $colname = $programme_field->colname;
            // make sure the field is being used (if it's in section 0 then it isn't)
            if ($programme_field->section > 0)
            {
                $programme->$colname = Input::get($colname);
            }
        }
    }
}

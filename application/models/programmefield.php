<?php
class ProgrammeField extends Field
{
    public static $table = 'programmes_fields';
    
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
    
    public static $types = array(
        'NORMAL' => 0,
        'DEFAULT' => 1,
        'OVERRIDABLE_DEFAULT' => 2
    );
    
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
    * @param array $input_fields data from the form
    * @return object $programme_obj modified programme object
    */
    public static function assign_fields($programme_obj, $programme_fields, $input_fields)
    {
        foreach ($programme_fields as $programme_field)
        {
            $colname = $programme_field->colname;
            // make sure the field is being used (if it's in section 0 then it isn't)
            if ($programme_field->section > 0)
            {
                if (isset($input_fields[$colname])) {
                    $programme_obj->$colname = $input_fields[$colname];
                }
            }
        }
        return $programme_obj;
    }
}

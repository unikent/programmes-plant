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
}

<?php
class ProgrammeField extends Field
{
    public static $table = 'programmes_fields';
    
    public static function programme_fields_by_section()
    {
        //$sections = ProgrammeSection::all()->programmefields;
        
        //print_r($sections);exit;

		$options = array();

		foreach ($sections as $section) 
		{
    		if ($section->section > 0)
    		{
        		$options[$section->section][$section->order] = $section;
			}
		}

		return $options;
        //return self::where('active','=','1')->order_by('order','asc')->get();
    }
    
/*
    $model = $this->model.'Field';

        return  $model::where('active','=','1')->order_by('order','asc')->get();
        
*/
}

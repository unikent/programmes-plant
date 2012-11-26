<?php
class ProgrammeField extends Field
{
    public static $table = 'programmes_fields';

    public static $types = array(
    								'NORMAL' => 0,
    								'DEFAULT' => 1,
    								'OVERRIDABLE_DEFAULT' => 2
    							);

    public static function get_types_as_list(){
    	$list_types = array();
    	foreach (self::$types as $key => $value) {
    		$list_types[$value] = $key;
    	}
    	return $list_types;
    }
}

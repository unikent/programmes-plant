<?php
class Setting extends SimpleData
{
	
	public static $table = 'system_settings';
	public static $rules = array(
		'ug_current_year'  => 'required',
		'pg_current_year'  => 'required'
	);

	
}
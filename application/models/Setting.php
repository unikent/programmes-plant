<?php
class Setting extends SimpleData
{

	public static $table = 'system_settings';
	public static $rules = array(
		'ug_current_year'  => 'required|integer',
		'pg_current_year'  => 'required|integer'
	);

	protected static $setting_data = null;

	public static function get_setting($setting){
		// for test, we have no tables
		if (Request::env() == 'test'){return '2014';}

		if(static::$setting_data === null){
			$settings = static::find(1);
			static::$setting_data = ($settings !== null) ? $settings->attributes : array();
		}
		return isset(static::$setting_data[$setting]) ? static::$setting_data[$setting] : null;
	}

	
}
<?php

class URLParams {

	public static $year = '2014';
	public static $type = 'ug';
	public static $current_year = '2014';
	public static $fields = false;
	public static $mainpath = '';
	public static $is_year_sensitive = false;
	public static $is_level_sensitive = false;

	public static function init()
	{
		// what year are we viewing currently?
	    static::$year = (is_numeric(URI::segment(1))) ? URI::segment(1) : '2014';

	    // what's the base year?
		static::$current_year = '2014';
		
		// are we viewing programme fields?
		if (URI::segment(2) == 'fields')
		{
			static::$fields = true;
			static::$type = URI::segment(1);
		}

		// ug or pg?
		elseif (URI::segment(2) == 'pg')
		{
			static::$type = 'pg';
		}

		// useful in various places
		static::$mainpath = static::$year . '/' . static::$type . '/';
	}

	public static function set_is_year_sensitive(){

	}

	public static function set_is_level_sensitive(){
		
	}

}

// /2014/ug/
// /ug/fields
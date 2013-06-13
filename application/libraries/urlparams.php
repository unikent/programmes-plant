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

		// If on API url
		if(URI::segment(1) == 'api'){
			static::$year = URI::segment(2);
			static::$type = (URI::segment(3) == 'postgraduate') ? 'pg' : 'ug';
			return;
		}

		// useful in various places
		static::$mainpath = static::$year . '/' . static::$type . '/';
	}

	public static function set_is_year_sensitive(){

	}

	public static function set_is_level_sensitive(){
		
	}

	public static function get_type(){
		return static::$type;

	}

	public static function get_year(){
		return static::$year;
	}

}

URLParams::init();

// /2014/ug/
// /ug/fields
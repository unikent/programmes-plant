<?php

class URLParams {

	public static $year = '2014';
	public static $type = 'ug';
	public static $current_year = '2014';
	public static $fields = false;
	public static $mainpath = '';

	// header links params
	public static $no_header_links = false;
	public static $year_header_links_only = false;
	public static $type_header_links_only = false;

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
		// or immutables
		elseif (URI::segment(1) == 'fields'){
			static::$fields = true;
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

		static::set_header_links_params();
	}

	public static function get_type(){
		return static::$type;

	}

	public static function get_year(){
		return static::$year;
	}

	/*
	 * Set the various header link statuses
	 */
	public static function set_header_links_params(){
		
		static::$no_header_links = (
			URI::segment(1) == 'editor' ||
            URI::segment(1) == 'campuses' ||
            URI::segment(1) == 'faculties' ||
            URI::segment(1) == 'awards' ||
            URI::segment(1) == 'leaflets' ||
            URI::segment(1) == 'schools' ||
            URI::segment(1) == 'subjects' ||
            URI::segment(1) == 'subjectcategories' ||
            URI::segment(1) == 'users'
        );

        static::$year_header_links_only = (
        	is_numeric(URI::segment(1)) && URI::segment(1) == 'globalsettings'
        );

        static::$type_header_links_only = static::url_segment_is_type(URI::segment(1));
	}


	public static function url_segment_is_type($segment){
		return strtolower($segment) == 'ug' || strtolower($segment) == 'pg';
	}



}

URLParams::init();

// /2014/ug/
// /ug/fields
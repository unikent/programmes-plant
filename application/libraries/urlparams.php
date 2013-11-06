<?php

class URLParams {

	public static $year = '';
	public static $type = 'ug';
	public static $current_year = '';
	public static $fields = false;
	public static $mainpath = '';

	// header links params
	public static $no_header_links = false;
	public static $year_header_links = false;
	public static $type_header_links = false;
	public static $header_path_params = array();

	public static function init()
	{
		// Load defaults
		$default_year = Setting::get_setting('ug_editing_year');
		//Ensure there is a default
		if($default_year===null) $default_year = 2014;

		static::$year = static::$current_year = $default_year;

		// what year are we viewing currently?
	    static::$year = (is_numeric(URI::segment(1))) ? URI::segment(1) : $default_year;

	    // what's the base year?
		static::$current_year = $default_year;
		
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
		elseif (URI::segment(1) == 'pg')
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

		if(!static::$type_header_links){
			static::$type = Session::get('last_type');
		}

		Session::put('last_type', static::$type);
	}

	public static function get_type($model=''){
		if ($model == 'GlobalSettingField') return 'ug';
		return static::$type;

	}

	public static function get_year(){
		return static::$year;
	}

	/*
	 * Set the various header link statuses
	 */
	public static function set_header_links_params(){

        static::$year_header_links = (
        	static::url_segment_is_year(URI::segment(1))
        );

        static::$type_header_links = (
        	static::url_segment_is_type(URI::segment(2)) ||
        	static::url_segment_is_type(URI::segment(1))
        );
		
		static::$no_header_links = (
			!static::$year_header_links &&
			!static::$type_header_links
        );

        if(static::$no_header_links){
        	static::$header_path_params = array();
        }

        elseif(static::$year_header_links && !static::$type_header_links){
        	static::$header_path_params = array('year' => static::$year);
        }

        elseif(!static::$year_header_links && static::$type_header_links){
        	static::$header_path_params = array('type' => static::$type);
        }

        else{
        	static::$header_path_params = array('year' => static::$year, 'type' => static::$type);
        }
	}

	/*
	 * Check that a segment of the URL is up or pg
	 * @param (string) $segment A segment of the URL
	 */
	public static function url_segment_is_type($segment){
		return strtolower($segment) == 'ug' || strtolower($segment) == 'pg';
	}

	/*
	 * Check that a segment of the URL is a year
	 * @param (string) $segment A segment of the URL
	 */
	public static function url_segment_is_year($segment){
		return is_numeric($segment) && 
				strlen($segment) == 4 && 
				intval($segment) > 2000 && 
				intval($segment) < 2100;
	}

	/*
	 * Work out the prefix of the current URL, replacing any params that need replacing
	 *
	 * @param (array) $params Any params that need replacing (year or type)
	 * @return 
	 */
	public static function get_variable_path_prefix($params = array()){
		
		if(empty(static::$header_path_params)){
        	return '';
        }

        foreach (static::$header_path_params as $key => $value) {
        	if(isset($params[$key])){
        		static::$header_path_params[$key] = $params[$key];
        	}
        }

		$header_path_prefix = '';

		foreach (static::$header_path_params as $key => $value) {
			$header_path_prefix .= $value . '/';
		}

		return $header_path_prefix;
	}

	/*
	 * Strip the year and type from the current URL if there are any
	 *
	 * @return (string) URL with year and type stripped out
	 */
	public static function strip_year_and_type_from_url(){
		
		$new_url = '';
		$segments_count = count(URI::$segments);

		foreach (URI::$segments as $key => $segment) {
			
			if(!static::url_segment_is_type($segment) && !static::url_segment_is_year($segment)){
				$new_url .= $segment;
				$new_url .= ($key == $segments_count - 1) ? '' : '/';
			}

		}

		return $new_url;
		
	}



}

URLParams::init();


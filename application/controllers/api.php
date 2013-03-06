<?php

class API_Controller extends Base_Controller {

	public $restful = true;

	/**
	 * Array to store headers as header => value.
	 * 
	 * Static so that potentially other classes could arbitarily add or modify headers here.
	 */
	public static $headers = array();
	
	public function __construct()
	{
		// turn off the profiler because this interferes with the web service
		Config::set('application.profiler', false);

		static::$headers['Cache-Control'] = 'public, max-age=3600'; 
	}
	
	/**
	* Get the index data
	*
	* @param  int     $year     Year of index to get.
	* @param  string  $format   Format, either XML or JSON.
	* @return string  json|xml  Data as a string or HTTP response.
	*/
	public function get_index($year, $format = 'json')
	{
		// get last generated date
		$last_generated = API::get_last_change_time();
		
		// If cache is valid, send 304
		if($this->cache_still_valid($last_generated)){
			return Response::make('', '304');
		}

		$index_data = API::get_index($year, 'ug');

		// 204 is the HTTP code for No Content - the result processed fine, but there was nothing to return.
		if (! $index_data)
		{
			// Considering 501 (server error) as more desciptive
			// The server either does not recognize the request method, or it lacks the ability to fulfill the request
			return Response::make('', 501);
		}

		static::$headers['Last-Modified'] = API::get_last_change_date_for_headers($last_generated);

		// Return the cached index file with the correct headers.
		return ($format=='xml') ? static::xml($index_data) : static::json($index_data, 200);
	}

	/**
	* Get subjects index
	*
	* @param  int     $year     Year of index to get.
	* @param  string  $format   Format, either XML or JSON.
	* @return string  json|xml  Data as a string or HTTP response.
	*/
	public function get_subject_index($year, $format = 'json')
	{
		// Get last updated date from cache
		$last_generated = API::get_last_change_time();
		// If cache is valid, send 304
		if($this->cache_still_valid($last_generated)){
			return Response::make('', '304');
		}

		//Get subjects
		$subjects = API::get_subjects_index($year, 'ug');

		if (! $subjects) return Response::make('', 501);

		static::$headers['Last-Modified'] = API::get_last_change_date_for_headers($last_generated);

		// output
		return ($format=='xml') ? static::xml($subjects) : static::json($subjects, 200);
	}
	
	/**
	* Get data for the programme as JSON.
	*
	* @param string $year          The Year.
	* @param int    $programme_id  The programme we're pulling data for.
	* @param string $format        Return in XML or JSON.      
	* @return Response             json|xml data as a string or HTTP response.    
	*/
	public function get_programme($year, $programme_id, $format = 'json')
	{
		// If cache is valid, send 304
		$last_modified = API::get_last_change_time();

		if($this->cache_still_valid($last_modified))
		{
			return Response::make('', '304');
		}

		try 
		{
			$programme = API::get_programme($programme_id, $year);
		}
		
		// Required data is missing?
		catch(MissingDataException $e)
		{
			return Response::make('', 501);
		}
		catch(NotFoundException $e)
		{
			return Response::make('', 404);
		}
		
		// Unknown issue with data.
		if (! $programme)
		{
			return Response::make('', 501);
		}

		// Set the HTTP Last-Modified header to the last updated date.
		static::$headers['Last-Modified'] = API::get_last_change_date_for_headers($last_modified);
		
		// return a JSON version of the newly-created $final object
		return ($format=='xml') ? static::xml($programme) : static::json($programme, 200);
	}


	/**
	 * get_data Return data from simpleData cache
	 *
	 * @param string $type.   Type of data to return, ie. Campuses
	 * @param string $format  Return in JSON or XML.
	 * @return Response       json|xml data as a string or HTTP response.    
	 */
	public function get_data($type, $format = 'json')
	{
		// If cache is valid, send 304
		$last_modified = API::get_last_change_time();

		if($this->cache_still_valid($last_modified)){
			return Response::make('', '304');
		}

		// Set data for cache
		static::$headers['Last-Modified'] = API::get_last_change_date_for_headers($last_modified);

		// If data exists, send it, else 404
		try
		{
			$data = API::get_data($type);
			return ($format=='xml') ? static::xml($data) : static::json($data, 200);
		}
		catch(NotFoundException $e)
		{
			return Response::make('', 404);
		}
	}

	/**
	 * Is cache still valid?
	 *
	 * @param int $last_modified.  Unix timestamp of when last change to system data was made.
	 * @return bool true|false     If cached data is still valid.
	 */
	protected function cache_still_valid($last_modified)
	{
		// There is no cache (hence its invalid)
		if(!Request::header('if-modified-since')) return false;

		// Unknown data of last change, to be safe assume cache is invalid
		if($last_modified === null) return false;

		// Get "if-modified-since" header as a timestamp
		$last_retrived = Request::header('if-modified-since');
		$request_modified_since = strtotime($last_retrived[0]);

		// If time the client created its cache ($request_modified_since) is after (or equal to) 
		// the last change made to the data ($last_modified) then it is still valid.
		return ($last_modified <= $request_modified_since);
	}

	/**
	 * Get preview
	 *
	 * @param  string $hash   The hash of the preview.
	 * @return string $format The format of the response, JSON or XML.
	 */
	public function get_preview($hash, $format='json')
	{
		try 
		{
			$programme = API::get_preview($hash);
		}
		catch(NotFoundException $e)
		{
			// Required data is missing?
			return Response::make('', 404);
		}

		return ($format=='xml') ? static::xml($programme) : static::json($programme);
	}


	/**
	* Output as XML
	*
	* @param mixed $data         To be shown as XML
	* @param int   $code         HTTP code to return.
	* @param array $add_headers  Additional headers to add to output.
	*/
	public static function xml($data, $code = 200, $add_headers = false)
	{
		static::$headers['Content-Type'] = 'application/xml';

		if ($add_headers)
		{
			$headers = array_merge(static::$headers, $add_headers);
		}

		return Response::make(API::array_to_xml($data), 200, static::$headers);
	}
	
	/**
	* Output as JSON
	*
	* @param mixed $data        To be shown as JSON.
	* @param int   $code        HTTP code to return.
	* @param array $add_headers Additional headers to add to output.
	*/
	public static function json($data, $code = 200, $add_headers = false)
	{
		static::$headers['Content-Type'] = 'application/json';

		if ($add_headers)
		{
			static::$headers = array_merge(static::$headers, $add_headers);
		}

		return Response::json($data, $code, static::$headers);
	}

	/**
	 * Return an XCRI-CAP feed for all programmes in a given year.
	 * 
	 * @param string $year year to generate xcri-cap of.
	 * @param string $level Either undergraduate or postgraduate.
	 * @return Response An XCRI-CAP field of the programmes for that year.
	 */
	public function get_xcri_cap($year, $level)
	{
		// get last generated date
		$last_generated = API::get_last_change_time();
		
		// If cache is valid, send 304
		if($this->cache_still_valid($last_generated)){
			return Response::make('', '304');
		}

		// pull from cache or send a 404
		$cache_key = "xcri-cap-ug-$year";
		$xcri = (Cache::has($cache_key)) ? Cache::get($cache_key) : false;

		if(!$xcri){
			return Response::make('', '404');
		}

		//atempt gzipping the feed
		$xcri = static::gzip($xcri);

		// set the content-type header
		static::$headers['Content-Type'] = 'text/xml';
		
		//send xcri-cap as our response
		return Response::make($xcri, 200, static::$headers);
	}

	/**
	 * Generate and cache an XCRI-CAP feed for all programmes in the given year.
	 * 
	 * @param string $year year to generate xcri-cap of.
	 * @param string $level Either undergraduate or postgraduate.
	 * @return Response An XCRI-CAP field of the programmes for that year.
	 */
	public static function generate_xcri_cap($year, $level){

		// get a list of all out programmes through the API
		$api_index = API::get_index($year, $level);

		$date = array();

		// fetch each programme individually for our xcri feed
		foreach (array_keys($api_index) as $programme_id) {
			$data['programmes'][] = API::get_xcrified_programme($programme_id, $year);
		}

		// if there are no programmes throw a 501 error
		if (! $data['programmes'])
		{
			Response::make('', 501);
		}

		// get the global settings for our xcri feed
		$globalsettings	= GlobalSetting::get_api_data($year);

		// if there are no global settings throw a 501 error
		if (! $globalsettings)
		{
			Response::make('', 501);
		}

		// neaten up the global settings
		$data['globalsettings'] = new StdClass();
		foreach ($globalsettings as $key => $value) {
			$key = GlobalSetting::trim_id_from_field_name($key);
			$data['globalsettings']->$key = $value;
		}

		// assemble the xcri-cap xml
		$xcri_xml = View::make('xcri-cap.1-2', $data);

		// cache the xcri-cap xml before sending it
		$cache_key = "xcri-cap-ug-$year";;
		Cache::put($cache_key, $xcri_xml, 2628000);

		return $xcri_xml;

	}

	/**
	 * gzip the content if the request can handle gzipped content
	 *
	 * @param $content The string to gzip
	 * @return $content Hopefully gzipped
	 */
	public static function gzip($content)
	{
		// what do we have in our Accept-Encoding headers
		$HTTP_ACCEPT_ENCODING = isset($_SERVER["HTTP_ACCEPT_ENCODING"]) ? $_SERVER["HTTP_ACCEPT_ENCODING"] : ''; 
	    
		// set the right encoding
		if( headers_sent() ) 
	        $encoding = false; 
	    else if( strpos($HTTP_ACCEPT_ENCODING, 'x-gzip') !== false ) 
	        $encoding = 'x-gzip'; 
	    else if( strpos($HTTP_ACCEPT_ENCODING,'gzip') !== false ) 
	        $encoding = 'gzip'; 
	    else 
	        $encoding = false;
		
	    if($encoding){
			// Add the appropriate encoding header and gzip our content
	    	static::$headers['Content-Encoding'] = $encoding;
	    	$content = "\x1f\x8b\x08\x00\x00\x00\x00\x00" . gzcompress($content);
	    }

	    return $content;
	}
}
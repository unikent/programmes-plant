<?php

class API_Controller extends Base_Controller {

	public $restful = true;
	
	public function __construct()
	{
		// turn off the profiler because this interferes with the web service
		Config::set('application.profiler', false);
	}
	
	/**
	* get the index data
	*
	* @param $year
	* @param $level - ug or pg
	* @param $format XML|JSON
	* @return json|xml data as a string or HTTP response
	*/
	public function get_index($year, $level, $format = 'json')
	{
		// get last generated date
		$last_generated = API::get_last_change_time();
		// If cache is valid, send 304
		if($this->cache_still_valid($last_generated)){
			return Response::make('', '304');
		}

		$index_data = API::get_index($year, $level);

		// 204 is the HTTP code for No Content - the result processed fine, but there was nothing to return.
		if (! $index_data)
		{
			// Considering 501 (server error) as more desciptive
			// The server either does not recognize the request method, or it lacks the ability to fulfill the request
			return Response::make('', 501);
		}

		$headers = array('Last-Modified' => API::get_last_change_date_for_headers($last_generated));

		// Return the cached index file with the correct headers.
		return ($format=='xml') ? static::xml($index_data) : static::json($index_data, 200, $headers);
	}

	/**
	* get subjects index
	*
	* @param $year
	* @param $level - ug or pg
	* @param $format XML|JSON
	* @return json|xml data as a string or HTTP response
	*/
	public function get_subject_index($year, $level, $format = 'json'){

		// Get last updated date from cache
		$last_generated = API::get_last_change_time();
		// If cache is valid, send 304
		if($this->cache_still_valid($last_generated)){
			return Response::make('', '304');
		}

		//Get subjects
		$subjects = API::get_subjects_index($year, $level);

		if (! $subjects) return Response::make('', 501);

		$headers = array('Last-Modified' => API::get_last_change_date_for_headers($last_generated));
		$headers['Cache-Control'] = 'public';

		// output
		return ($format=='xml') ? static::xml($subjects) : static::json($subjects, 200, $headers);
	}
	
	/**
	* Get data for the programme as JSON.
	*
	* @param string $year The Year.
	* @param string $level The level - ug or pg.
	* @param $programme_id The programme we're pulling data for.
	* @param $format XML|JSON
	* @return json|xml data as a string or HTTP response.    
	*/
	public function get_programme($year, $level, $programme_id, $format = 'json')
	{
		// If cache is valid, send 304
		$last_modified = API::get_last_change_time();
		if($this->cache_still_valid($last_modified)){
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

		$headers = array();

		// Set the HTTP Last-Modified header to the last updated date.
		$headers['Last-Modified'] = API::get_last_change_date_for_headers($last_modified);

		// Set a less conservative caching policy.
		$headers['Cache-Control'] = 'public';
		
		// return a JSON version of the newly-created $final object
		return ($format=='xml') ? static::xml($programme) : static::json($programme, 200, $headers);
	}


	/**
	 * get_data Return data from simpleData cache
	 *
	 * @param $type. Type of data to return, ie. Campuses
	 * @param $format XML|JSON
	 * @return json|xml data as a string or HTTP response.    
	 */
	public function get_data($type, $format = 'json'){

		// If cache is valid, send 304
		$last_modified = API::get_last_change_time();
		if($this->cache_still_valid($last_modified)){
			return Response::make('', '304');
		}

		// Set data for cache
		$headers['Last-Modified'] = API::get_last_change_date_for_headers($last_modified);
		$headers['Cache-Control'] = 'public';

		// If data exists, send it, else 404
		try{
			$data = API::get_data($type);
			return ($format=='xml') ? static::xml($data) : static::json($data, 200, $headers);
		}catch(NotFoundException $e){
			return Response::make('', 404);
		}
	}

	/**
	 * Is cache still valid
	 *
	 * @param $last_modified. Unix timestamp of when last change to system data was made.
	 * @return true|false If cached data is still valid
	 */
	protected function cache_still_valid($last_modified){

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
	 * 
	 */
	public function get_preview($hash, $format='json')
	{
		try {
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
	* @param $data to be shown as XML
	* @param int $code HTTP code to return.
	* @param array $add_headers Additional headers to add to output.
	*/
	public static function xml($data, $code = 200, $add_headers = false)
	{
		$headers = array();

		$headers['Content-Type'] = 'application/json';

		if ($add_headers)
		{
			$headers = array_merge($headers, $add_headers);
		}

		return Response::make(API::array_to_xml($data), 200, $headers);
	}
	
	/**
	* Output as JSON
	*
	* @param $data to be shown as JSON
	* @param int $code HTTP code to return.
	* @param array $add_headers Additional headers to add to output.
	*/
	public static function json($data, $code = 200, $add_headers = false)
	{

		$headers = array();

		$headers['Content-Type'] = 'application/json';

		if ($add_headers)
		{
			$headers = array_merge($headers, $add_headers);
		}

		return Response::json($data, $code, $headers);
	}
	

}
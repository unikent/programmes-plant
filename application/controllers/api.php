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
		$index_data = API::get_index($year, $level);

		// 204 is the HTTP code for No Content - the result processed fine, but there was nothing to return.
		if (! $index_data)
		{
			// Considering 501 (server error) as more desciptive
			// The server either does not recognize the request method, or it lacks the ability to fulfill the request
			return Response::error('501');
		}

		// Return the cached index file with the correct headers.
		return ($format=='xml') ? static::xml($index_data) : static::json($index_data);
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
		//Get subjects
		$subjects = API::get_subjects_index($year, $level);

		if (! $subjects) return Response::error('501');

		// output
		return ($format=='xml') ? static::xml($subjects) : static::json($subjects);
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
		if (Request::header('if-modified-since'))
		{
			$header = Request::header('if-modified-since');
			$request_modified_since = strtotime($header[0]);

			$time_modified = ProgrammeRevision::where('year', '=', $year)
							 	->where('programme_id', '=', $programme_id)
							 	->where('status', '=', 'live')
								->get(array('published_at'));

			// Check we got a programme back!
			if ($time_modified == null)
			{
				return Response::make('', '404');
			}

			$programme_last_modified = strtotime($time_modified[0]->published_at);

			// Check this logic is actually right!
			if ($programme_last_modified == $request_modified_since)
			{
				return Response::make('', '304');
			}
		}

		try {
			$programme = API::get_programme($programme_id, $year);
		}
		catch(MissingDataException $e)
		{
			// Required data is missing?
			return Response::error('501');
		}
		catch(NotFoundException $e)
		{
			// Page does not exist / isn't published
			return Response::error('404');
		}
		
		// Unknown issue with data.
		if (! $programme)
		{
			return Response::error('501');
		}

		$headers = array();

		// Set the HTTP Last-Modified header to the date that the programme was published.
		$headers['Last-Modified'] = $programme['published_at'];
		
		// return a JSON version of the newly-created $final object
		return ($format=='xml') ? static::xml($programme) : Response::json($programme, 200, $headers);
	}

	/**
	 * Get preview
	 *
	 * 
	 */
	public function get_preview($hash, $format='json'){
		try {
			$programme = API::get_preview($hash);
		}
		catch(NotFoundException $e)
		{
			// Required data is missing?
			return Response::error('404');
		}

		return ($format=='xml') ? static::xml($programme) : static::json($programme);
	}


	/**
	* Output as XML
	*
	* @param $data to be shown as XML
	*/
	public static function xml($data){
		return Response::make(API::array_to_xml($data), 200, array('Content-Type' => 'text/xml'));
	}
	
	/**
	* Output as JSON
	*
	* @param $data to be shown as JSON
	*/
	public static function json($data){
		return Response::json($data, '200', array('Content-Type' => 'application/json'));
	}
	

}
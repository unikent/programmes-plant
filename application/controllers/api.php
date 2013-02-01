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
		// Get the correct modified time for the index.
		$last_generated = ApiIndexTime::where('year', '=', $year)->where('level', '=', 'ug')->first();

		// This index should exist. If it doesn't its a problem with the caching.
		if ($last_generated == null)
		{
			return Response::make('', 501);
		}

		if (Request::header('if-modified-since'))
		{
			$header = Request::header('if-modified-since');
			$request_modified_since = strtotime($header[0]);

			$programme_index_last_modified = strtotime($last_generated->updated_at);

			if ($programme_index_last_modified == $request_modified_since)
			{
				return Response::make('', 304);
			}
		}

		$index_data = API::get_index($year, $level);

		// 204 is the HTTP code for No Content - the result processed fine, but there was nothing to return.
		if (! $index_data)
		{
			// Considering 501 (server error) as more desciptive
			// The server either does not recognize the request method, or it lacks the ability to fulfill the request
			return Response::make('', 501);
		}

		$headers = array('Last-Modified' => $last_generated->updated_at);

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
		//Get subjects
		$subjects = API::get_subjects_index($year, $level);

		if (! $subjects) return Response::make('', 501);

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

			if ($programme_last_modified == $request_modified_since)
			{
				return Response::make('', '304');
			}
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

		$response = new Response($programme, 200, $headers); 

		// Set the HTTP Last-Modified header to the date that the programme was published.
		$headers['Last-Modified'] = $programme['published_at'];

		// Set a less conservative caching policy.
		//$response->foundation->set('cache-control', 'public');
		dd($response->foundation);
		
		// return a JSON version of the newly-created $final object
		//return $response->send();
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
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
	* @return json data as a string or HTTP response
	*/
	public function get_index($year, $level)
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
		return Response::json($index_data, '200', array('Content-Type' => 'application/json'));
	}
	
	/**
	* Get data for the programme as JSON.
	*
	* @param string $year The Year.
	* @param string $level The level - ug or pg.
	* @param $programme_id The programme we're pulling data for.
	* @return json data as a string or HTTP response.    
	*/
	public function get_programme($year, $level, $programme_id)
	{
		$programme = API::get_programme($programme_id, $year);
		//Programme::get_as_flattened($year, $level, $programme_id);

		// 204 is the HTTP code for No Content - the result processed fine, but there was nothing to return.
		// This is the case if certain aspects are not in place for our JSON.
		if (! $programme)
		{
			return Response::error('501');
		}
		
		// return a JSON version of the newly-created $final object
		return Response::json($programme);
	}

	// XML output method
	public static function xml($data){
		return Response::make(API::array_to_xml($data), 200, array('Content-Type' => 'text/xml'));
	}
	

}
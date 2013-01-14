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
		$json_index = Programme::json_index($year, $level);

		// 204 is the HTTP code for No Content - the result processed fine, but there was nothing to return.
		if (! $json_index)
		{
			return Response::error('204');
		}

		// Return the cached index file with the correct headers.
		return Response::make($json_index, '200', array('Content-Type' => 'application/json'));
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
		$programme = Programme::get_as_flattened($year, $level, $programme_id);

		// 204 is the HTTP code for No Content - the result processed fine, but there was nothing to return.
		// This is the case if certain aspects are not in place for our JSON.
		if (! $programme)
		{
			return Response::error('204');
		}
		
		// return a JSON version of the newly-created $final object
		return Response::json($programme);
	}

}
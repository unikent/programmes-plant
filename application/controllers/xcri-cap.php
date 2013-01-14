<?php

class XCRI_CAP_Controller extends Base_Controller {

	public $restful = true;

	public function __construct()
	{
		// Turn off Profiler as this interferes with what is a web service.
		Config::set('application.profiler', false);
	}

	/**
	 * Return an XCRI-CAP feed for all programmes in a given year.
	 * 
	 * @param string $level Either undergraduate or postgraduate.
	 * @return Response An XCRI-CAP field of the programmes for that year.
	 */
	public function get_index($level, $year)
	{
		// Get only live programmes.
		$data['programmes'] = Programme::where('year', '=', $year)->where('live', '=', true)->get();

		// We need some additional data to fill the XCRI-CAP feed entirely.
		$data['globalsettings'] = GlobalSetting::where('year', '=', $year)->first();
		
		$xcri = View::make('xcri-cap.1-2', $data);

		$headers = array(
			'Content-Type' => 'text/xml'
		);

		return Response::make($xcri, 200, $headers);
	}

}
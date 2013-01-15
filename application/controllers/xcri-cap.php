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
		$programmes = Programme::with('award', 'campus', 'subject_area_1')->where('year', '=', $year)->where('live', '=', true)->get();

		if (! $programmes)
		{
			Response::error(404);
		}

		$data = array('programmes' => array());
		
		foreach($programmes as $programme)
		{
			$data['programmes'][] = $programme->xcrify();
		}

		// We need some additional data to fill the XCRI-CAP feed entirely.
		$globalsettings = GlobalSetting::where('year', '=', $year)->first();

		if (! $globalsettings)
		{
			Response::error(404);
		}

		$data['globalsettings'] = $globalsettings->trim_ids_from_field_names();

		$xcri = View::make('xcri-cap.1-2', $data);

		$headers = array(
			'Content-Type' => 'text/xml'
		);

		return Response::make($xcri, 200, $headers);
	}

}
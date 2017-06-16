<?php

require_once path('base') . 'vendor/autoload.php';

class sitsModule_Task {

	public static $moduleCache = array();

	/**
	 * Run the moduledata command.
	 *
	 * @param array  $arguments The arguments sent to the moduledata command.
	 */
	public function run($arguments = array())
	{
		//$this->purgeOldPGData($this->processYears['pg']);
		//$this->purgeOldUGData($this->processYears['ug']);

		//Connect to SITS
		$courses = DB::connection('sqlsrv')->query("SELECT * FROM [sitswebtest].[dbo].[programmes_plant_header] WHERE pp_id != ''");

		//$this->purgeOldPGData(2016);


		foreach ($courses as $course)
		{
			$this->create_delivery($course);
		}
		//Create for courses that don't exist
	}

	public function purgeOldPGData($year) {
		$to_del = DB::table('programmes_pg')->where('year', '=', $year)->lists('id');
		DB::table('pg_programme_deliveries')->where_in('programme_id',$to_del)->delete();
	}

	/**
	 * Remove all UG deliveries
	 */
	public function purgeOldUGData($year) {
		$to_del = DB::table('programmes_ug')->where('year', '=', $year)->lists('id');
		DB::table('ug_programme_deliveries')->where_in('programme_id',$to_del)->delete();
	}

	public function getCurrentYear( $level )
	{
		return Setting::get_setting( $level . "_current_year" );
	}

	public function create_delivery($course)
	{
		$delivery_class = "UG_Delivery";
		$award_class = "UG_Award";

		if($course->pp_prospectus == 'PG'){
			$delivery_class = "PG_Delivery";
			$award_class = "PG_Award";
		}

		// Quick dependency injector
		$delivery = new $delivery_class;

		$delivery->programme_id = $course->pp_id;


		if(	$award_class == "UG_Award")
		{
			$award = $course->pp_award_id_ug;
		}
		else
		{
			$award = $course->pp_award_id_pg;
		}

		$delivery->award = empty($award) ? 0 : $award;

		$delivery->pos_code = $course->pos_code;
		$delivery->mcr = $course->mcr_code;
		$delivery->ari_code = $course->ari_code;
		$delivery->description = $course->mcr_name;
		$delivery->attendance_pattern = $course->attendance_mode = 'FT' ? 'full-time' : 'part-time';

		$delivery->save();
	}
}
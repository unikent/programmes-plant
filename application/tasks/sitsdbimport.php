<?php

require_once path('base') . 'vendor/autoload.php';

use Kent\Log;

/**
 * This task file is used to load an XML feed provided from SITS
 * and import the data into the Programmes Plant
 *
 * Data imported is IPO, POS code, MCR code, ARI code, CRS code, description,
 * award and attendance type (full-time or part-time)
 *
 * The IPO, MCR, and ARI codes are used to direct users to the appropriate course in SITS
 * when they click the 'apply', 'enquire', or 'order prospectus' links on a course page.
 *
 * The CRS code is used as the ID for Discover Uni widgets and replaces the previously
 * manually produced KISCOUREID
 */
class SITSDBImport_Task
{
	/**
	 * Persists the cURL object.
	 */
	public $curl = false;

	/**
	 * Boolean that sets if we want to use a proxy in CURL
	 */
	public $proxy = false;

	/**
	 * The location of a HTTP proxy if required.
	 */
	public $proxy_server = '';

	/**
	 * The port of a HTTP proxy if required.
	 */
	public $proxy_port = '';

	public $processYears = array();
	public $seenProgrammes = array();
	public $ipos = array();
	public $path = '/www/live/shared/shared/data/SITSCourseData/SITSCourseData.xml';

	public function run($args = array())
	{
		// login as initial user so changes to programs can be saved since these
		// are Auth'd in the model
		Auth::login(1);
		if (!Auth::user()) {
			Log::error('Initial User not found. Quiting.');
			die();
		}

		$parameters = $this->parse_arguments($args);
	  // display help if needed
		if (isset($parameters['help'])) {
			echo $parameters['help'];
			exit;
		}

		foreach ($parameters['year'] as $type => $year) {
			$this->processYears[$type] = ($year=='current')?$this->getCurrentYear($type):$year;
		}

		Log::$logfile = path('storage') . '/logs/sits_db_import.log';
		Log::purge();

		$data = $this->load_sitsimport_data('pg', $this->processYears['pg']);
		// if we have no data, exit
		if (!$data) {
			exit;
		}

		$courseGroupings = [];
		foreach ($data as $delivery) {
				//only add delivery if it is new or has an MCR that is lower than the previous one
				if (
					isset($courseGroupings[$delivery->pp_page_id][$delivery->sits_apply_link_code1]) &&
					intval($delivery->sits_apply_link_code2) > intval($courseGroupings[$delivery->pp_page_id][$delivery->sits_apply_link_code1]->sits_apply_link_code2)
				) {
					continue;
				}

				$courseGroupings[$delivery->pp_page_id][$delivery->sits_apply_link_code1] = $delivery;
		}

		// If XML file is good, purge old caches before starting import
		$this->purgeOldPGData($this->processYears['pg']);
		$this->purgeOldUGData($this->processYears['ug']);
		
		foreach ($courseGroupings as $id => $deliveries) {

			$course = new stdClass();

			$course->deliveries = $deliveries;
			$firstDelivery = null;
			foreach ($deliveries as $key => $delivery) {
			 	$firstDelivery = $delivery;
			 	break;
			 }
			
		  //get rid of 'UG'/'PG' that SITS concat to our progsplant ID
			// $course->pos = $firstDelivery->sds_pos_code;
			$course->level = strtolower(substr($firstDelivery->sits_study_level, 0, 2)); // or pp_award_level?
			$course->year = $this->processYears[$course->level];
			// $course->mcr = $firstDelivery->sits_apply_link_code1;
			// $course->crs = $firstDelivery->pp_discover_uni_id;
			// $course->ari_code = $firstDelivery->sits_enquiry_link_code;
			// $course->description = $firstDelivery->sits_course_title_full;
			// $course->attendanceType = $firstDelivery->sits_attend_mode;
			$course->programme   = $this->getProgramme($course, $firstDelivery->pp_page_id);

			if (empty($course->programme) || !is_object($course->programme)) {
				continue;
			}

			$this->updateKISCourseID($course);
			
			echo $course->programme->instance_id . " =>". PHP_EOL . implode(PHP_EOL, array_map(function($delivery){
				return $delivery->sits_academic_year 
						. " " . $delivery->sits_study_level 
						. " " . $delivery->sds_pos_code 
						. " " . $delivery->sits_apply_link_code1 
						. " " . $delivery->sits_apply_link_code2 
						. " " . $delivery->pp_award_id 
						. " " . $delivery->pp_discover_uni_id;
			}, $deliveries)) . PHP_EOL;
			continue;
			
			URLParams::$type = $course->level;
			$delivery = $this->createDelivery($course);
		}
	
	  // clear output cache
		API::purge_output_cache();
	}
	
	/**
	 * updateKISCourseID
	 *
	 * create a new revision of $programme with the
	 * crs code in $course if needed
	 *
	 * @param  object $course
	 * @param  object $programme
	 * @return void
	 */
	public function updateKISCourseID($course)
	{
		$programme = $course->programme;
		$kiscoursefield = $programme::get_kiscourseid_field();

		if (!$kiscoursefield) {
			// don't update KISCOURSEID if programme does not have one
			return;
		}

		if (!isset($course->crs)) {
			// don't update KISCOURSEID if we do not have a CRS Code
			return;
		}

		if ($course->programme->current_revision != $course->programme->live_revision) {
			// don't update course if an edit is currently in progress
			return;
		}

		if ($course->programme->$kiscoursefield == (string)$course->crs) {
			// avoid updating ID if we don't need to
			return;
		}
		
		$course->programme->$kiscoursefield = (string)$course->crs;
		$course->programme->save();
		$course->programme->make_revision_live($course->programme->current_revision);
	}
  /**
   * Remove all PG deliveries
   */
	public function purgeOldPGData($year)
	{
		$to_del = DB::table('programmes_pg')->where('year', '=', $year)->lists('id');
		DB::table('pg_programme_deliveries')->where_in('programme_id', $to_del)->delete();
	}

  /**
   * Remove all UG deliveries
   */
	public function purgeOldUGData($year)
	{
		$to_del = DB::table('programmes_ug')->where('year', '=', $year)->lists('id');
		DB::table('ug_programme_deliveries')->where_in('programme_id', $to_del)->delete();
	}

	public function loadXML($path)
	{

		libxml_use_internal_errors(true);
		
		if (filemtime($path) < (time()-(24 * 60 * 60))) {
			Log::error('XML file has not been modified for more than 24 hours.');
		}

		$courses = simplexml_load_file($path);

		if ($courses === false) {
			Log::error('XML file does not exist or is invalid.');
			foreach (libxml_get_errors() as $error) {
				Log::error($error->message);
			}
			exit;
		}

		return $courses;
	}

  /**
   * Get the live Programmes Plant Year for each level
   */
	public function getCurrentYear($level)
	{
		return Setting::get_setting($level . "_current_year");
	}

	/**
	 * checkCourseIsUndergraduateAndAppliedToDirectly
	 *
	 * checks to see if a given course is an undergraduate course which accepts direct (SITS) applications
	 * Notes:
	 * - ug courses have an MCR starting with 'U' for undergraduate
	 * - direct application courses have an MCR ending with 'D' for direct
	 *
	 * @param mixed $course
	 * @return bool
	 */
	public function checkCourseIsUndergraduateAndAppliedToDirectly($course)
	{
		$mcr = (string)$course->mcr;
		
		// invalid mcr
		if (0 == strlen($mcr)) {
			return false;
		}

		// is ug and can be applied to directly
		if (('U' === $mcr[0]) && ('D' === $mcr[strlen($mcr)-1])) {
			return true;
		}
		
		return false;
	}

	/**
	 * checkCourseIsInUse
	 * is this course open for direct applications via sits
	 *
	 * @param mixed $course
	 * @return bool
	 */
	public function checkCourseIsInUse($course)
	{
		if ($course->inUse != 'Y') {
			return false;
		}
		return true;
	}

	public function checkIPOIsValid($ipo)
	{
		if ($ipo->inUse != 'Y') {
			return false;
		}
		return true;
	}

	public function trimPOSCode($pos)
	{
		return preg_replace("/\d+$/", "", $pos);
	}

  /**
   * Use the concatenated XML progID element from SITS
   * to determine whether we update UG or PG
   */
	public function getCourseLevel($course)
	{
		if (stripos($course->progID, 'ug') !== false) {
			return 'ug';
		}
		return 'pg';
	}

	public function getProgramme($course, $programme_id)
	{
		$model = $course->level === "ug" ? "UG_Programme" : "PG_Programme";
		
		return $model::where(
			"instance_id",
			"=",
			$programme_id
		)->where(
			"year",
			"=",
			$this->processYears[$course->level]
		)->first();
	}

  /**
   * Create deliveries for Postgraduate courses
   * We add a number of fields from the XML to the database in
   * this function.
   */
	public function createDelivery($course)
	{
		$delivery_class = "PG_Delivery";
		$award_class = "PG_Award";

		if ($course->level === 'ug') {
			$delivery_class = "UG_Delivery";
			$award_class = "UG_Award";
		}

		$delivery = new $delivery_class;

		$delivery->programme_id = $course->programme->instance_id;

		$award = intval($course->award);
		$delivery->award = empty($award) ? 0 : $award;

		$delivery->pos_code = (string)$course->pos;
		$delivery->mcr = (string)$course->mcr;
		$delivery->ari_code = (string)$course->ari_code;
		$delivery->description = (string)$course->description;
		$delivery->attendance_pattern = strtolower($course->attendanceType);

		$delivery->current_ipo = $this->extractCurrentIPO($course, $course->year);
		$delivery->previous_ipo='';

		$delivery->save();

		return $delivery;
	}

  /**
   * Get the IPOs that are inUse in SITS
   * We use this function to get the sequence number for the relevant academicYear
   */
	public function extractCurrentIPO($course, $year)
	{

		if ($course->inUse == 'N') {
			return "";
		}

		foreach ($course->ipo as $ipo) {
			if (intval($ipo->academicYear) - 1 === intval($year) && $ipo->inUse == 'Y') {
				return (string)$ipo->sequence;
			}
		}

		return "";
	}

  /**
   * parse_arguments - parses command line options
   *
   * @param array $arguments
   * @return array $parameters
   */
	public function parse_arguments($arguments = array())
	{

	  // set defaults for the parameters in case they're not set
		$parameters = array();
		$parameters['year'] = array('pg'=>'current','ug'=>'current');
		$parameters['path'] = $this->path;
		
		
		foreach ($arguments as $argument) {
			$switch_name = substr($argument, 0, 2);
			switch ($switch_name) {
				// level
				case '-p':
					$parameters['year']['pg'] = str_replace('-p', '', $argument) != '' ? str_replace('-p', '', $argument) : 'current';
					break;
				// programme session
				case '-u':
					$parameters['year']['ug'] = str_replace('-u', '', $argument) != '' ? str_replace('-u', '', $argument) : 'current';
					break;
				// XML file path
				case '-f':
					$parameters['path'] = str_replace('-f', '', $argument) != '' ? str_replace('-f', '', $argument) : $parameters['path'];
					break;
				default:
					$parameters['help'] = $this->help_argument();
			}
		}
		return $parameters;
	}

		/**
	 * fetch the modules structure for a given programe
	 * @param string $level - ug or pg
	 * @param int $year - programmes plant id
	 *
	 * @return stdClass module stages for the programme
	 */
	public function load_sitsimport_data($level, $year)
	{
		if (empty($level) || empty($year)) {
			return false;
		}
		
		// build request
		$webservice_request = Config::get('sitsimport.api_base') . "/v1/programme-deliveries/${year}/${level}";
		
		// load data
		echo "Requesting: " . $webservice_request . ' - ';
		
		$this->curl = new \Curl($webservice_request);
		
		// don't verify ssl
		$this->curl->ssl(false);
		
		// a GET web service
		$this->curl->http_method = 'get';
		
		// set a timeout
		$timeout = 30;
		$this->curl->option(CURLOPT_TIMEOUT, $timeout);
		
		// proxy if required
		if ($this->proxy)
		{
			$this->curl->proxy($this->proxy_server, $this->proxy_port);
		}
		$response = $this->curl->execute();

		if (!$response) {
			$errorsMessage = 'Error getting delivery data from API:' . $webservice_request;
			echo $errorsMessage;
			Log::error($errorsMessage);
			return false;
		}
		
		return json_decode($response);
	}

	/**
	* Set a HTTP proxy for the request.
	* 
	* @param string $proxy_server The URL of the proxy server.
	* @param int $port The port of the proxy server.
	*/
	public function set_proxy($proxy_server, $proxy_port = 3128) 
	{
		$this->proxy = true;
		$this->proxy_server = $proxy_server;
		$this->proxy_port = $proxy_port;
	}

	/**
	 * help_argument - display usage summary
	 *
	 * @return void
	 */
	public function help_argument()
	{
		return <<< TXT
-p - postgraduate year. Defaults to current.
-u - undergraduate year. Defaults to current.
-f - path to XML feed from SITS feed. Defaults to $this->path


TXT;
	}
}

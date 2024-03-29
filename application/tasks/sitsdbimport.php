<?php

require_once path('base') . 'vendor/autoload.php';

use Kent\Log;

/**
 * This task file is used to load delivery data from an api.kent endpoint which fetches,
 * deliveries from SITS, and import the data into Programmes Plant
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

		// get values for ug year and pg year
		foreach ($parameters['year'] as $type => $year) {
			$this->processYears[$type] = ($year=='current')?$this->getCurrentYear($type):$year;
		}

		Log::$logfile = path('storage') . '/logs/sits_db_import.log';
		Log::purge();
		
		foreach ($this->processYears as $level => $year) {
			$data = $this->load_sitsimport_data($level, $year);
			// if we have no data, exit
			if (!$data) {
				continue;
			}

			// group deliveries by programme and mcr code
			$courseGroupings = [];
			foreach ($data as $delivery) {
				//only add delivery if it is new or has an MCR that is lower than the previous one
				if (isset($courseGroupings[$delivery->pp_page_id][$delivery->sits_apply_link_code1]) &&
					intval($delivery->sits_apply_link_code2) > intval($courseGroupings[$delivery->pp_page_id][$delivery->sits_apply_link_code1]->sits_apply_link_code2)
				) {
					continue;
				}

				$courseGroupings[$delivery->pp_page_id][$delivery->sits_apply_link_code1] = $delivery;
			}
			
			// remove old deliveries for this level and yesr
			$this->purgeOldData($level, $year);

			foreach ($courseGroupings as $programme_id => $deliveries) {
				$firstDelivery = null;
				foreach ($deliveries as $mcr => $delivery) {
					$firstDelivery = $delivery;
					break;
				}
				
				$programme = $this->getProgramme($programme_id, $level);

				if (empty($programme) || !is_object($programme)) {
					continue;
				}

				$this->updateKISCourseID($programme, $firstDelivery->pp_discover_uni_id);
				
				URLParams::$type = $level;
				foreach ($deliveries as $mcr => $deliveryData) {
					$delivery = $this->createDelivery($level, $programme, $deliveryData);

					echo "Added: " . $delivery->programme_id
						. " | " . $delivery->pos_code
						. " | " . $delivery->mcr
						. " | " . $delivery->current_ipo . PHP_EOL;
				}
			}
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
	public function updateKISCourseID($programme, $crs)
	{
		$kiscoursefield = $programme::get_kiscourseid_field();

		if (!$kiscoursefield) {
			// don't update KISCOURSEID if programme does not have one
			return;
		}

		if (!isset($crs)) {
			// don't update KISCOURSEID if we do not have a CRS Code
			return;
		}

		if ($programme->current_revision != $programme->live_revision) {
			// don't update course if an edit is currently in progress
			return;
		}

		if ($programme->$kiscoursefield == (string)$crs) {
			// avoid updating ID if we don't need to
			return;
		}
		
		$programme->$kiscoursefield = (string)$crs;
		$programme->save();
		$programme->make_revision_live($programme->current_revision);
	}
   /**
	 * Remove existing deliveries for a given academic year and study level
	 *
	 * @param  string $level (ug or pg)
	 * @param  string $year (academic year)
	 * @return void
	 */
	public function purgeOldData($level, $year)
	{
		if ($level === 'pg') {
			$to_del = DB::table('programmes_pg')->where('year', '=', $year)->lists('id');
			if (!empty($to_del)) {
				DB::table('pg_programme_deliveries')->where_in('programme_id', $to_del)->delete();
			}
		}
		
		if ($level === 'ug') {
			$to_del = DB::table('programmes_ug')->where('year', '=', $year)->lists('id');
			if (!empty($to_del)) {
				DB::table('ug_programme_deliveries')->where_in('programme_id', $to_del)->delete();
			}
		}
	}

	/**
	 * Get the live Programmes Plant Year for each level
	 *
	 * @param  string $level (ur or pg)
	 * @return string the programmes plant year of application
	 */
	public function getCurrentYear($level)
	{
		return Setting::get_setting($level . "_current_year");
	}

	public function getProgramme($id, $level)
	{
		$model = $level === "ug" ? "UG_Programme" : "PG_Programme";
		
		return $model::where(
			"instance_id",
			"=",
			$id
		)->where(
			"year",
			"=",
			$this->processYears[$level]
		)->first();
	}

	/**
	 * Create deliveries for a programme
	 *
	 * @param  string $level
	 * @param  string $programme
	 * @param  object $deliveryData
	 *
	 * @return object delivery
	 */
	public function createDelivery($level, $programme, $deliveryData)
	{
		$deliveryClass = "PG_Delivery";

		if ($level === 'ug') {
			$deliveryClass = "UG_Delivery";
		}

		$delivery = new $deliveryClass;

		$delivery->programme_id = $programme->id;

		$award = intval($deliveryData->pp_award_id);
		$delivery->award = empty($award) ? 0 : $award;

		$delivery->pos_code = (string)$deliveryData->sds_pos_code ;
		$delivery->mcr = (string)$deliveryData->sits_apply_link_code1;
		$delivery->ari_code = (string)$deliveryData->sits_enquiry_link_code;
		$delivery->description = (string)$deliveryData->sits_course_title_full;
		$delivery->attendance_pattern = strtolower($deliveryData->sits_attend_mode);

		$delivery->current_ipo = $deliveryData->sits_apply_link_code2;
		$delivery->previous_ipo='';

		$delivery->save();

		return $delivery;
	}

  /**
   * parse_arguments - parses command line options
   *
   * @param array $arguments
   *
   * @return array $parameters
   */
	public function parse_arguments($arguments = array())
	{

	  // set defaults for the parameters in case they're not set
		$parameters = array();
		$parameters['year'] = array('pg'=>'current','ug'=>'current');
		
		
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
				default:
					$parameters['help'] = $this->help_argument();
			}
		}
		return $parameters;
	}

	/**
	 * fetch the deliveries for a given level/year
	 * @param string $level - ug or pg
	 * @param int $year - programmes plant id
	 *
	 * @return array|false delivery data
	 */
	public function load_sitsimport_data($level, $year)
	{
		if (empty($level) || empty($year)) {
			return false;
		}
		
		// build request
		$webservice_request = Config::get('sitsimport.api_base') . "/v1/programme-deliveries/${year}/${level}";
		
		// load data
		echo "Requesting: " . $webservice_request . ' - ' . PHP_EOL;
		
		$this->curl = new \Curl($webservice_request);
		
		// don't verify ssl
		$this->curl->ssl(false);
		
		// a GET web service
		$this->curl->http_method = 'get';
		
		// set a timeout
		$timeout = 30;
		$this->curl->option(CURLOPT_TIMEOUT, $timeout);
		
		// proxy if required
		if ($this->proxy) {
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


TXT;
	}
}

<?php

use Kent\Log;

/**
 * This task retrieves data about programmes from SITS via api.kent
 * and imports the data into the Programmes Plant (pg|ug)_programme_deliveries tables.
 *
 * A logfile is generated (purged at the start of each request) in {storage}/logs/sits_import.log.
 *
 * Another script "sitsimport_check.php" can be run to check if this log file contains any errors.
 *
 * The data imported is:
 *
 * - MCR code - "Marketing Course Record" - Identifies a programme that can be applied to
 * - IPO - Institution Published Programme Occurrence - Seems to be a 4-digit (zero-padded) number...
 * 		  (shire docs say A single start date for an IPP (MCR) - but looks more like a version or something...)
 * - POS code - Code identifying a Programme of Study.
 * - ARI code - Area of Interest code.
 * - description,
 * - award
 * - attendance type (full-time or part-time)
 *
 * The IPO, MCR, and ARI codes are used to direct users to the appropriate course in SITS
 * when they click the 'apply', 'enquire', or 'order prospectus' links on a course page (of-course).
 *
 * The data was previously imported from an XML file provided by SITS.
 *
 * The task imports a single undergraduate (ug) year and a single postgraduate (pg) year worth of
 * programmes at at time. It defaults to "current" for both years if not specified.
 *
 * The value "current" can be substituted instead of an actual year.
 *
 * Usage:
 * - php artisan sitsimport -u2018 (import data from undergraduate 2018 programmes and postgraduate "current")
 * - php artisan sitsimport -pcurrent (import data from current year postgraduate programmes and for undergraduate "current")
 * - php artisan sitsimport -u2018 -p2018 (import data from undergraduate and postgraduate 2018 programmes)
 */
class SITSImport_Task {

  public $processYears = array();
  public $seenProgrammes = array();

  public function run( $args = array() ) {

    $parameters = $this->parse_arguments($args);

    // display help if needed
    if ( isset($parameters['help']) ) {
      echo $parameters['help'];
      exit;
    }

    foreach ($parameters['year'] as $level=>$year){
      $this->processYears[$level] = ($year=='current')?$this->getCurrentYear( $level ):$year;
    }

    Log::$logfile = path('storage') . '/logs/sits_import.log';
    Log::purge();

	foreach ($this->processYears as $level => $year) {

	    $data = $this->loadProgrammeDeliveries($level, $year);

	    if(!$data) {
	      Log::error("Unable to fetch {$level} {$year} programme deliveries from Kent API.");
	      exit;
	    }

	    // If API data is good, purge old caches before starting import
	    if ($level == 'pg') {
	    	$this->purgeOldPGData($this->processYears[$level]);
	    }
	    else {
	    	$this->purgeOldUGData($this->processYears[$level]);
	    }

	    foreach ( $data as $delivery ) {

	      $programme   = $this->getProgramme( $delivery, $level );
	      $year = $this->processYears[$level];

	      if ( empty( $programme ) || !is_object( $programme ) ) {
	        continue;
	      }

	      URLParams::$type = $level;
	      $this->createDelivery( $delivery, $programme, $year, $level );
	    }
	}

    // clear output cache
    API::purge_output_cache();

  }

  /**
   * Remove all PG deliveries for the specified year
   * @param string $year - The year
   */
  public function purgeOldPGData($year) {
    $to_del = DB::table('programmes_pg')->where('year', '=', $year)->lists('id');
    DB::table('pg_programme_deliveries')->where_in('programme_id',$to_del)->delete();
  }

  /**
   * Remove all UG deliveries for the specified year
   * @param string $year - The year
   */
  public function purgeOldUGData($year) {
    $to_del = DB::table('programmes_ug')->where('year', '=', $year)->lists('id');
    DB::table('ug_programme_deliveries')->where_in('programme_id',$to_del)->delete();
  }

	/**
	 * Retrieve programme delivery information from the API.
	 * @param null $level
	 * @param null $year
	 * @return bool|array - false if error, otherwise an array of objects like:
	 *	[
	 * 	 {
	 *		"in_use": "Y",
	 *		"ipo_seqn": "0005",
	 *		"academic_year": "2018",
	 *		"start_date": "Sep 16 2017 12:00:00:000AM",
	 *		"close_date": "Sep 16 2017 12:00:00:000AM",
	 *		"mcr_code": "UACFECO201BA-PD",
	 *		"mcr_name": "Accounting and Finance and Economics ",
	 *		"crs_code": "UACFECO2X2BA-F",
	 *		"pp_award_id_ug": "2",
	 *		"pp_award_id_pg": null,
	 *		"campus_id": "UKC",
	 *		"campus_name": "Canterbury",
	 *		"attendance_mode": "PT",
	 *		"ari_code": "MCR000001366",
	 *		"pp_id": "1",
	 *		"pp_prospectus": "UG",
	 *		"pos_code": "ACCF-ECON:BA2"
	 *	},
	 * ...
	 * ]
	 */
  public function loadProgrammeDeliveries($level = null, $year = null) {
  	$url = Config::get('application.api_base') .
		'/v1/sits/programmesheader' .
		(empty($level) ? '' : '/' . $level).
		(empty($year) ? '' : '/' . (intval($year) - 1));
    $ch = curl_init(
      $url
    );

    curl_setopt($ch, CURLOPT_HTTPGET, true);
    // curl_setopt($ch, CURLOPT_PROXY, 'advocate.kent.ac.uk:3128');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);

    if (!curl_errno($ch)) {
      switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
        case 200:
            // cache results?
          break;
        default:
          // unexpected HTTP code, do something?
      }
    }
    else {
    	Log::error(curl_error($ch));
	}

    curl_close($ch);

    return $result ? json_decode($result) : false;
  }

  /**
   * Get the live Programmes Plant Year for each level
   * @param string $level - Either 'ug' or 'pg' (for undergraduate or postgraduate)
   * @return int - The current year.
   */
  public function getCurrentYear( $level ) {
    return Setting::get_setting( $level . "_current_year" );
  }

  /**
   * Use the concatenated XML progID element from SITS
   * to determine whether we update UG or PG
   */
  public function getCourseLevel( $delivery ) {
    return strtolower($delivery->pp_prospectus);
  }

	/**
	 * Get a programme model from either the programmes_pg (UG_Programme) or programmes_ug (PG_Programm) table
	 * @param object $delivery - Delivery object item from the array returned by $this->loadProgrammeDeliveries()
	 * @param string $level - 'ug' or 'pg' (undergraduate or postgraduate)
	 * @param null|array $processYears
	 * @return UG_Programme|PG_Programme|null - The programme that matches the delivery, level and year.
	 */
  public function getProgramme( $delivery, $level, $processYears = null ) {
    if ($processYears === null) {
      $processYears = $this->processYears;
    }

    $model = $level === "ug" ? "UG_Programme" : "PG_Programme";
    ;

    return $model::where(
      "instance_id", "=", $delivery->pp_id
    )->where(
      "year", "=", $processYears[$level]
    )->first();
  }

  /**
   * Create deliveries for Postgraduate courses
   * We add a number of fields from the XML to the database in
   * this function.
   */
  public function createDelivery( $api_delivery, $programme, $year, $level, $delivery = null ) {
    $delivery_class = "PG_Delivery";
    $award_class = "PG_Award";

    if($level === 'ug'){
      $delivery_class = "UG_Delivery";
      $award_class = "UG_Award";
    }

    // Quick dependency injector
    if ($delivery === null) {
      $delivery = new $delivery_class;
    }

    $delivery->programme_id = $programme->id;

    $award = intval($api_delivery->{"pp_award_id_" . $this->getCourseLevel($api_delivery)});
    $delivery->award = empty($award) ? 0 : $award;

    $delivery->pos_code = (string)$api_delivery->pos_code;
    $delivery->mcr = (string)$api_delivery->mcr_code;
    $delivery->ari_code = (string)$api_delivery->ari_code;
    $delivery->description = (string)$api_delivery->mcr_name;
    $delivery->attendance_pattern = strtolower( $api_delivery->attendance_mode ) === 'pt' ? 'part-time' : 'full-time';

    $delivery->current_ipo = $api_delivery->ipo_seqn;
    $delivery->previous_ipo='';

    $delivery->save();

    return $delivery;
  }

  /**
   * parse_arguments - parses command line options
   *
   * @param array $arguments
   * @return array $parameters
   */
  public function parse_arguments($arguments = array()) {
    // set defaults for the parameters in case they're not set
    $parameters = array();
    $parameters['year'] = array('pg'=>'current','ug'=>'current');

    foreach ($arguments as $argument) {
      $switch_name = substr($argument, 0, 2);

      switch($switch_name) {
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

  public function help_argument() {
    return "\n\n-p - postgraduate year. Defaults to current.\n-u - undergraduate year. Defaults to current.\n\n";
  }

}

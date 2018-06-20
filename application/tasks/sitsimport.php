<?php

use Kent\Log;

/**
 * This task file is used to load an XML feed provided from SITS
 * and import the data into the Programmes Plant
 *
 * Data imported is IPO, POS code, MCR code, ARI code, description,
 * award and attendance type (full-time or part-time)
 *
 * The IPO, MCR, and ARI codes are used to direct users to the appropriate course in SITS
 * when they click the 'apply', 'enquire', or 'order prospectus' links on a course page.
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

    foreach ($parameters['year'] as $type=>$year){
      $this->processYears[$type] = ($year=='current')?$this->getCurrentYear( $type ):$year;
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
	    $this->purgeOldPGData($this->processYears[$level]);

		# code...
	    foreach ( $data as $delivery ) {

	      //get rid of 'UG'/'PG' that SITS concat to our progsplant ID
	      $delivery->pos = $this->trimPOSCode( $delivery->pos );
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
   * Remove all PG deliveries
   */
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

  public function loadProgrammeDeliveries($level = null, $year = null) {
    $ch = curl_init(
      Config::get('application.api_base') .
      '/v1/sits/programmesheader' .
      (empty($level) ? '' : '/' . $level).
      (empty($year) ? '' : '/' . (intval($year) - 1))
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

    curl_close($ch);

    return $result ? json_decode($result) : false;
  }

  /**
   * Get the live Programmes Plant Year for each level
   */
  public function getCurrentYear( $level ) {
    return Setting::get_setting( $level . "_current_year" );
  }

  public function trimPOSCode( $pos ) {
    return preg_replace( "/\d+$/", "", $pos );
  }

  /**
   * Use the concatenated XML progID element from SITS
   * to determine whether we update UG or PG
   */
  public function getCourseLevel( $delivery ) {
    return strtolower($delivery->pp_prospectus);
  }

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

<?php

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
  public $ipos = array();

  public function run( $args = array() ) {

    $parameters = $this->parse_arguments($args);
    // display help if needed
    if ( isset($parameters['help']) )
    {
      echo $parameters['help'];
      exit;
    }

    foreach ($parameters['year'] as $type=>$year){
      $this->processYears[$type] = ($year=='current')?$this->getCurrentYear( $type ):$year;
    }
    // Load XML file
    $xml = $this->loadXML();

    // If XML file is good, purge old caches before starting import
    $this->purgeOldPGData($this->processYears['pg']);
    $this->purgeOldUGData($this->processYears['ug']);

    foreach ( $xml as $course ) {
      //make sure it has a programme ID in the progsplant
      if ( !$this->checkCourseIsValid( $course ) ) {
        continue;
      }

      $this->ipos = array();

      foreach ( $course->ipo as $ipo ) {
        //only get IPOs that are in use in SITS
        if ( !$this->checkIPOIsValid( $ipo ) ) {
          continue;
        }

        $this->ipos[] = $ipo;
      }

      //get rid of 'UG'/'PG' that SITS concat to our progsplant ID
      $course->pos = $this->trimPOSCode( $course->pos );
      $courseLevel = $this->getCourseLevel( $course );
      $programme   = $this->getProgramme( $course, $courseLevel );
      $year = $this->processYears[$courseLevel];

      if ( empty( $programme ) || !is_object( $programme ) ) {
        continue;
      }


      URLParams::$type = $courseLevel;
      $delivery = $this->createDelivery( $course, $programme, $year, $courseLevel );


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

  public function loadXML() {

    $courses = simplexml_load_file( '/www/live/shared/shared/data/SITSCourseData/SITSCourseData.xml' );

    if ( $courses === false ) {
      throw new Exception( 'XML file does not exist in this location' );
      exit;
    }

    return $courses;
  }

  /**
   * Get the live Programmes Plant Year for each level
   */
  public function getCurrentYear( $level ) {
    return Setting::get_setting( $level . "_current_year" );
  }

  public function checkCourseIsValid( $course ) {
    if ( $course->progID == '' ) {
      return false;
    }
    return true;
  }

  public function checkIPOIsValid( $ipo ) {
    if ( $ipo->inUse != 'Y' ) {
      return false;
    }
    return true;
  }

  public function trimPOSCode( $pos ) {
    return preg_replace( "/\d+$/", "", $pos );
  }

  /**
   * Use the concatenated XML progID element from SITS
   * to determine whether we update UG or PG
   */
  public function getCourseLevel( $course ) {
    if ( stripos( $course->progID, 'ug' ) !== false ) {
      return 'ug';
    }
    return 'pg';
  }

  public function getProgramme( $course, $level, $processYears = null ) {
    if ($processYears === null) {
      $processYears = $this->processYears;
    }

    $model = $level === "ug" ? "UG_Programme" : "PG_Programme";
    $courseID = substr( $course->progID, 0, count( $course->progID ) - 3 );

    return $model::where(
      "instance_id", "=", $courseID
    )->where(
      "year", "=", $processYears[$level]
    )->first();
  }

  /**
   * Create deliveries for Postgraduate courses
   * We add a number of fields from the XML to the database in
   * this function.
   */
  public function createDelivery( $course, $programme, $year, $level, $delivery = null ) {
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

    $award = intval($course->award);
    $delivery->award = empty($award) ? 0 : $award;

    $delivery->pos_code = (string)$course->pos;
    $delivery->mcr = (string)$course->mcr;
    $delivery->ari_code = (string)$course->ari_code;
    $delivery->description = (string)$course->description;
    $delivery->attendance_pattern = strtolower( $course->attendanceType );

    $delivery->current_ipo = $this->extractCurrentIPO( $course, $year );
    $delivery->previous_ipo='';

    $delivery->save();

    return $delivery;
  }

  /**
   * Get the IPOs that are inUse in SITS
   * We use this function to get the sequence number for the relevant academicYear
   */
  public function extractCurrentIPO( $course, $year ) {

    if ( $course->inUse == 'N' ) {
      return "";
    }

    foreach ( $course->ipo as $ipo ) {
      if ( intval( $ipo->academicYear ) - 1 === intval( $year ) && $ipo->inUse == 'Y' ) {
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

    foreach ($arguments as $argument)
    {
      $switch_name = substr($argument, 0, 2);
      switch($switch_name)
      {
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

  public function help_argument()
  {
    return "\n\n-p - postgraduate year. Defaults to current.\n-u - undergraduate year. Defaults to current.\n\n";
  }

}

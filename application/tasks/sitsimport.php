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

  public $currentYears = array();
  public $seenProgrammes = array();
  public $ipos = array();

  public function run( $args = array() ) {

    $this->currentYears["ug"] = $this->getCurrentYear( "ug" );
    $this->currentYears["pg"] = $this->getCurrentYear( "pg" );

    // Load XML file
    $xml = $this->loadXML();

    // If XML file is good, purge old caches before starting import
    $this->purgeOldPGData();
    $this->purgeOldUGData( $this->currentYears["ug"] );

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
      $year = $this->currentYears[$courseLevel];

      if ( empty( $programme ) || !is_object( $programme ) ) {
        continue;
      }


      if ( $courseLevel === "pg" ) {
        URLParams::$type = "pg";

        $delivery = $this->createDelivery( $course, $programme, $year );
      } elseif ( $courseLevel === "ug" ) {
        $this->updateUGSITSData( $course, $programme, $year );
      }

      // Clear UG programme cache (PG doesnt need this clearing as it uses deliveries)
      ug_programme::purge_internal_cache($year);
    }
    
    // clear output cache
    API::purge_output_cache();

  }

  /**
   * Deliveries table is separate for PG so we can
   * truncate the data in this table
   */
  public function purgeOldPGData() {
    return DB::query( 'DELETE FROM pg_programme_deliveries' );
  }

  /**
   * For UG we need to delete fields from the 2 relevant tables
   * that will be replaced as part of this task
   */
  public function purgeOldUGData( $year ) {
    foreach ( array( 'programmes_ug', 'programmes_revisions_ug' ) as $table ) {
      DB::table( $table )
      ->where( 'year', '=', $year )
      ->update( array(
          'fulltime_mcr_code_88' => '',
          'parttime_mcr_code_87' => '',
          'pos_code_44' => '',
          'ari_code' => '',
          'current_ipo_pt' => '',
          'previous_ipo_pt' => '' ) );
    }
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

  public function getProgramme( $course, $level, $currentYears = null ) {
    if ($currentYears === null) {
      $currentYears = $this->currentYears;
    }

    $model = $level === "ug" ? "UG_Programme" : "PG_Programme";
    $courseID = substr( $course->progID, 0, count( $course->progID ) - 3 );

    return $model::where(
      "instance_id", "=", $courseID
    )->where(
      "year", "=", $currentYears[$level]
    )->first();
  }

  /**
   * Create deliveries for Postgraduate courses
   * We add a number of fields from the XML to the database in
   * this function.
   */
  public function createDelivery( $course, $programme, $year, $delivery = null ) {
    // Quick dependency injector
    if ($delivery === null) {
      $delivery = new PG_Deliveries;
    }

    $delivery->programme_id = $programme->id;

    $award = PG_Award::where(
      "longname", "=", $course->award
    )->first();

    $delivery->award = empty( $award ) ? 0 : $award->id;

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
   * Get the attendanceType (full-time or part-time)
   * Call to various functions to update the IPO data
   */
  public function updateUGSITSData( $course, $programme, $year ) {

    $type = strtolower( $course->attendanceType );

    $revisions = $programme->get_revisions();

    $sequenceNumber = $this->extractCurrentIPO( $course, $programme->year );

    // update all the revisions
    foreach ( $revisions as $revision ) {
      $this->updateUGRecord( $course, $revision, $type, $sequenceNumber );
    }
    // update real programme
    $this->updateUGRecord( $course, $programme, $type, $sequenceNumber );
  }

  /**
   * Update the UG fields in both the programmes_ug and revisions tables
   */
  public function updateUGRecord( $course, $programme, $type, $sequenceNumber ) {

    $programme->pos_code_44 = "$course->pos";
    
    if ( $type == "part-time" ) 
    {
      $programme->parttime_mcr_code_87 = "$course->mcr";
      $programme->current_ipo_pt = $sequenceNumber;
      $programme->ari_code = "$course->ari_code";
    }
    elseif ( $type == "full-time" ) 
    {
      $programme->fulltime_mcr_code_88 = "$course->mcr";
      $programme->ft_ari_code = "$course->ari_code";
    }

    $programme->raw_save();
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

}

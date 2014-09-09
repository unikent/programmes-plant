<?php

class SITSImport2_Task {

  private $currentYears = array();
  private $seenProgrammes = array();
  private $ipos = array();

  public function run($args = array()) {

    $currentYears["ug"] = $this->getCurrentYear("ug");
    $currentYears["pg"] = $this->getCurrentYear("pg");

    $this->purgeOldPGData();
    $this->purgeOldUGData($currentYears["ug"]);
    
    $xml = $this->loadXML();

    foreach ($xml as $course) {
      if (!checkCourseIsValid($course)){
        continue;
      }

      $this->ipos = array();

      foreach ($course->ipo as $ipo) {
        if (!checkIPOIsValid($ipo)){
          continue;
        }

        $this->ipos[] = $ipo;
      }

      $course->pos = $this->trimPOSCode($course->pos);
      $courseLevel = $this->getCourseLevel($course);
      $programme   = $this->getProgramme($course, $courseLevel);

      if (empty($programme) || !is_object($programme)) {
        continue;
      }

      /**
       * @todo - If programme is UG
       *    - Check if course is FT/PT
       *      - Set MCR code
       *      - Set POS code
       *      - Set ARI code
       *      - GET/SET revisions
       *      - Save programme & revisions to db
       */

      if ($courseLevel === "pg") {
        URLParams::$type = "pg";

        $delivery = $this->createDelivery($course, $programme);

        
      }
    }
    /**
     * @todo - Find live revision in db
     *        - If live revision exists
     *          - Call generate_api_programme
     */
    $this->purgeCache();

  }

  private function purgeOldPGData() {
    return DB::query('TRUNCATE TABLE pg_programme_deliveries');
  }

  private function purgeOldUGData($year) {
    foreach (array('programmes_ug', 'programmes_revisions_ug') AS $table) {
      DB::table($table)
      ->where('year', '=', $year)
      ->update(array(
        'fulltime_mcr_code_88' => '',
        'parttime_mcr_code_87' => '',
        'pos_code_44' => '',
        'ari_code' => '',
        'current_ipo_pt' => '',
        'previous_ipo_pt' => ''));
    }
  }

  private function loadXML() {
    $courses = simplexml_load_file('/www/live/shared/shared/data/SITSCourseData/SITSCourseData.xml');
    if ($courses === false) {
      throw new Exception('XML file does not exist in this location');
      exit;
    }

    return $courses;
  }

  private function getCurrentYear($level) {
    return Setting::get_setting($level . "_current_year");
  }

  private function checkCourseIsValid($course) {
    if ($course->progID == '') {
      return false;
    }
    return true;
  }

  private function checkIPOIsValid($ipo) {
    if ($ipo->inUse != 'Y') {
      return false;
    }
    return true;
  }

  private function trimPOSCode($pos) {
    return preg_replace("/\d+$/", "", $pos);
  }

  private function getCourseLevel($course) {
    if (stripos($course->progID, 'ug') !== false) {
      return 'ug';
    }
    return 'pg';
  }

  private function getProgramme($course, $level) {
    $model = $level === "ug" ? "UG_Programme" : "PG_Programme";
    $courseID = substr($course->progID, 0, count($course->progID) - 3);

    return $model::where(
      "instance_id", "=", $courseID
    )->where(
      "year", "=", $this->currentYears[$level]
    )->first();
  }

  private function createDelivery($course, $programme) {
    $delivery = new PG_Deliveries;
    $delivery->programme_id = $programme->id;

    $award = PG_Award::where(
      "longname", "=", $course->award
    )->first();

    $delivery->award = empty($award) ? 0 : $award->id;

    $delivery->pos_code = (string)$course->pos;
    $delivery->mcr = (string)$course->mcr;
    $delivery->ari_code = (string)$course->ari_code;
    $delivery->description = (string)$course->description;
    $delivery->attendance_pattern = strtolower($course->attendanceType);

    // This could probably be refactored further
    foreach ($this->ipos as $ipo) {
      if (isset($this->ipos["curr"])
        && isset($this->ipos["prev"])) {
        break;
      }

      if (!isset($this->ipos["curr"])
        && intval($ipo->academicYear - 1) === intval($programme->year)) {
        $delivery->current_ipo = (string)$ipo->sequence;
        $this->ipos["curr"] = $ipo;
        continue;
      }

      if (!isset($this->ipos["curr"])
        && intval($ipo->academicYear - 1) === intval($programme->year) - 1) {
        $delivery->previous_ipo = (string)$ipo->sequence;
        $this->ipos["prev"] = $ipo;
        continue;
      }
    }
    $delivery->save();
    
    return $delivery;
  }

    private function purgeCache() {
    try {
      Cache::purge('api-output-pg');
      Cache::purge('api-output-ug');
    } catch(Exception $e) { }
  }

}
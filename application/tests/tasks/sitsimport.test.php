<?php

require_once dirname( dirname( dirname( __FILE__ ) ) ) . '/tasks/sitsimport.php';

class TestSITSImport_Task extends PHPUnit_Framework_TestCase {

	private $validUGCourse = array( 'pp_prospectus' => 'UG', 'pp_id' => '394' );
	private $validPGCourse = array( 'pp_prospectus' => 'PG', 'pp_id' => '71' );

	private $fullCourse = array (
		'pp_prospectus' => 'PG'
		, 'pp_id' => '174'
		, 'pp_award_id_pg' => '1'
		, 'mcr_code' => 'PAAS000101MS-FD'
		, 'pos_code' => 'ACTSCIAP:MSC-T1'
		, 'ari_code' => 'MCR000000013'
		, 'mcr_name' => 'Applied Actuarial Science - MSc - full-time at Canterbury'
		, 'attendance_mode' => 'FT');

	private $task;

	public function setUp() {
		$this->validUGCourse = (object)$this->validUGCourse;
		$this->validPGCourse = (object)$this->validPGCourse;

		$this->fullCourse = (object)$this->fullCourse;

		$this->task = new SITSImport_Task();
	}

	public function testCurrentYear() {
		$currUGYear = $this->task->getCurrentYear( 'ug' );
		$currPGYear = $this->task->getCurrentYear( 'pg' );

		$this->assertTrue( is_numeric( $currUGYear ) );
		$this->assertTrue( is_numeric( $currPGYear ) );
	}

	public function testCourseLevelIsDetectedFromDelivery() {

		$ugCourse = $this->validUGCourse;
		$pgCourse = $this->validPGCourse;

		$this->assertEquals( $this->task->getCourseLevel( $this->validUGCourse), 'ug');
		$this->assertEquals( $this->task->getCourseLevel( $this->validPGCourse), 'pg');
	}

	public function testProgramme() {

		$ugCourse = $this->validUGCourse;
		$pgCourse = $this->validPGCourse;

		$currentYears = array("ug" => "2014", "pg" => "2014");

		$programme = $this->task->getProgramme( $this->fullCourse, 'pg', $currentYears );

		//assert to Null so we don't have to use the DB Model
		$this->assertNull($programme);

	}

}

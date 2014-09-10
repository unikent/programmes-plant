<?php

require_once dirname( dirname( dirname( __FILE__ ) ) ) . '/tasks/sitsimport.php';

class TestSITSImport_Task extends PHPUnit_Framework_TestCase {

	private $validUGCourse = array( 'progID' => '394UG' );
	private $validPGCourse = array( 'progID' => '71PG' );

	private $fullCourse = array (
		'progID' => '174PG'
		, 'award' => 'Master of Science'
		, 'mcr' => 'PAAS000101MS-FD'
		, 'pos_code' => 'ACTSCIAP:MSC-T1'
		, 'ari_code' => 'MCR000000013'
		, 'description' => 'Applied Actuarial Science - MSc - full-time at Canterbury'
		, 'attendance_pattern' => 'full-time');

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

	public function testCheckingCourseIsValid() {

		$invalidCourse = (object)array( 'progID' => '' );

        $this->assertTrue( $this->task->checkCourseIsValid( $this->validPGCourse ) );
        $this->assertFalse( $this->task->checkCourseIsValid( $invalidCourse ) );

	}

	public function testCheckingIPOIsValid() {
		$validIPO = array( 'inUse' => 'Y' );
		$invalidIPO = array( 'inUse' => 'N' );

		$this->assertTrue( $this->task->checkIPOIsValid( (object)$validIPO ) );
		$this->assertFalse( $this->task->checkIPOIsValid( (object)$invalidIPO ) );
	}

	public function testCourseLevelIsDetectedFromProgID() {

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

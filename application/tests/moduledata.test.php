<?php

require_once path('base') . 'vendor/autoload.php';

class TestModuleData extends PHPUnit_Framework_TestCase
{

	/**
	 * Set up the database.
	 */
	public static function setUpBeforeClass()
	{
	}

	public function tearDown()
	{
	}

	/**
	 * Populates the database with inputted array.
	 * 
	 * @param array $input An array of items to add the database.
	 * @return void
	 */
	public function populate($input)
	{
	}

	public function testParseArgumentsWhenAllValuesArePresent()
	{
	   $arguments = array('-lug', '-s2012', '-t2', '-c10', '-x', '-h');
	   $expected_parameters = array('type' => 'ug', 'programme_session' => '2012', 'sleeptime' => 2, 'counter' => 10, 'test_mode' => true, 'help' => "\n\n-l - ug or pg. Defaults to ug.\n-s - programme session. Defaults to 2014.\n-m - module session. Defaults to 2014\n-t - seconds per web service call. Defaults to 5 (one request every 5 seconds).\n-c - programmes to process. Defaults to 1. 0 indicates all.\n-x - test mode.\n\n");
	   $actual_parameters = Command::run(array('moduledata:parse_arguments', $arguments));
	   
	   $this->assertEquals($expected_parameters, $actual_parameters, "the moduledata:parse_arguments task did not return the right values for the parameters given.");
	}
	
	public function testParseArgumentsWhenAllValuesAreMissing()
	{
	   $arguments = array('-l', '-s', '-t', '-c', '-x', -'h');
	   $expected_parameters = array('type' => 'ug', 'programme_session' => '2014', 'sleeptime' => 5, 'counter' => 1, 'test_mode' => true, 'help' => "\n\n-l - ug or pg. Defaults to ug.\n-s - programme session. Defaults to 2014.\n-m - module session. Defaults to 2014\n-t - seconds per web service call. Defaults to 5 (one request every 5 seconds).\n-c - programmes to process. Defaults to 1. 0 indicates all.\n-x - test mode.\n\n");
	   $actual_parameters = Command::run(array('moduledata:parse_arguments', $arguments));
	   
	   $this->assertEquals($expected_parameters, $actual_parameters, "the moduledata:parse_arguments task did not return the right values for the parameters given.");
	}
	
	public function testParseArgumentsWhenNoArgsGiven()
	{
	   $arguments = array();
	   $expected_parameters = array('help' => "\n\n-l - ug or pg. Defaults to ug.\n-s - programme session. Defaults to 2014.\n-m - module session. Defaults to 2014\n-t - seconds per web service call. Defaults to 5 (one request every 5 seconds).\n-c - programmes to process. Defaults to 1. 0 indicates all.\n-x - test mode.\n\n");
	   $actual_parameters = Command::run(array('moduledata:parse_arguments', $arguments));
	   
	   $this->assertEquals($expected_parameters, $actual_parameters, "the moduledata:parse_arguments task did not return the right values for the parameters given.");
	}
	
	public function testParseArgumentsWhenUnknownArgsGiven()
	{
	   $arguments = array('-z');
	   $expected_parameters = array('help' => "\n\n-l - ug or pg. Defaults to ug.\n-s - programme session. Defaults to 2014.\n-m - module session. Defaults to 2014\n-t - seconds per web service call. Defaults to 5 (one request every 5 seconds).\n-c - programmes to process. Defaults to 1. 0 indicates all.\n-x - test mode.\n\n");
	   $actual_parameters = Command::run(array('moduledata:parse_arguments', $arguments));
	   
	   $this->assertEquals($expected_parameters, $actual_parameters, "the moduledata:parse_arguments task did not return the right values for the parameters given.");
	}
	
	
	public function testBuildProgrammeModulesTaskOutputsAsExpected()
	{
	   $this->module_data_obj = new ProgrammesPlant\ModuleData();
	   $this->module_data_obj->test_mode = true;
	   $url_programme_modules_full = dirname(dirname(__FILE__)).'/tests/data/programme_modules.json';
	   $url_synopsis = Config::get('module.module_data_url');
	   
	   $actual_output = Command::run(array('moduledata:build_programme_modules', $module_data_obj, $url_programme_modules_full, $url_synopsis, 1, 'ug', '2014'));
	   $expected_output = '';
	   
	   $this->assertEquals($expected_output, $actual_output, "the moduledata:build_programme_modules task did not return the right data for the parameters given.");
	}
	
	
	
}
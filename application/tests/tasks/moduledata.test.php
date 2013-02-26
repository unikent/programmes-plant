<?php

require_once(dirname(dirname(dirname(__FILE__ ))) . '/tasks/moduledata.php');

class TestModuleData_Task extends PHPUnit_Framework_TestCase {
    
    public $module_data_task = '';
    
    public function setUp()
    {
        $this->module_data_task = new ModuleData_Task();
    }
    
    public function tearDown()
    {
        unset($this->module_data_task);
        
/*
        $programmes = Programme::all();

		foreach ($programmes as $programme)
		{
			$programme->delete_for_test();
		}

		parent::tearDown();
*/
    }
    
    public function populate()
	{
		// Setup something we can edit.
		$input = array(
			'year' => '2014',
			'live' => '1',
			'created_by' => 'mb324',
			'programme_title_1' => 'Test',
			'pos_code_44' => 'ACCF-S:BA',
			
		);

		$programme = Programme::create($input);
		$programme->save();
	}
    
    public function testparse_argumentsShowsHelpOnEmptyArgs()
    {
        $parameters = $this->module_data_task->parse_arguments();
        $this->assertEquals($parameters['help'], "\n\n-l - ug or pg. Defaults to ug.\n-s - programme session. Defaults to 2014.\n-m - module session. Defaults to 2014\n-t - seconds per web service call. Defaults to 5 (one request every 5 seconds).\n-c - programmes to process. Defaults to 1. 0 indicates all.\n-x - test mode.\n\n");
    }
    
    public function argumentDataProvider()
    {
        return array(
            array(null, array('help' => "\n\n-l - ug or pg. Defaults to ug.\n-s - programme session. Defaults to 2014.\n-m - module session. Defaults to 2014\n-t - seconds per web service call. Defaults to 5 (one request every 5 seconds).\n-c - programmes to process. Defaults to 1. 0 indicates all.\n-x - test mode.\n\n")),
            array(array('-h'), array('help' => "\n\n-l - ug or pg. Defaults to ug.\n-s - programme session. Defaults to 2014.\n-m - module session. Defaults to 2014\n-t - seconds per web service call. Defaults to 5 (one request every 5 seconds).\n-c - programmes to process. Defaults to 1. 0 indicates all.\n-x - test mode.\n\n")),
            array(array('-lpg'), array('type' => 'pg'), true),
            array(array('-s2013'), array('programme_session' => '2013'), true),
            array(array('-t10'), array('sleeptime' => '10'), true),
            array(array('-c5'), array('counter' => '5'), true),
            array(array('-x'), array('test_mode' => true), true),
        );
        
    }
    
    /**
     * @dataProvider argumentDataProvider
     */
    public function testCorrectResponseToArguments($input, $expectation, $defaults = false)
    {
        $parameters = array();
        
        if ($defaults)
        {
            $parameters['type'] = 'ug';
            $parameters['programme_session'] = '2014';
            $parameters['sleeptime'] = 5;
            $parameters['counter'] = 1;
            $parameters['test_mode'] = false;
        }
        
        $output = $this->module_data_task->parse_arguments($input);
        
        $expectation = array_merge($parameters, $expectation);
        
        $this->assertEquals($expectation, $output);
    }
    
    public function testbuild_programme_modulesCachedSameAsExpected()
    {
        $programme_modules = $this->loadProgrammeModuleData();
        $module_synopsis = $this->loadModuleSynopsisData();
        $module_data_obj = $this->buildMock($programme_modules, $module_synopsis);
        $this->module_data_task->build_programme_modules($module_data_obj, '', '', 1, 'ug', '2014');
        
        $cache_key = "programme-modules.ug-2014-1";
        $output = json_encode(Cache::get($cache_key));
        
        // assert that what's in the cache matches what we expect
        $expected = $this->loadModuleData();
        
        $this->assertEquals($expected, $output);
    }
    
    public function testbuild_programme_modulesCachedSameAsExpectedForFoundation()
    {
        $programme_modules = $this->loadProgrammeModuleDataFoundation();
        $module_synopsis = $this->loadModuleSynopsisData();
        $module_data_obj = $this->buildMock($programme_modules, $module_synopsis);
        $this->module_data_task->build_programme_modules($module_data_obj, '', '', 1, 'ug', '2014');
        
        $cache_key = "programme-modules.ug-2014-1";
        $output = json_encode(Cache::get($cache_key));
        
        // assert that what's in the cache matches what we expect
        $expected = $this->loadModuleDataFoundation();
        
        $this->assertEquals($expected, $output);
    }
    
    public function testbuild_programme_modulesErrorsOnErrorMessage()
    {
        $return = $this->loadProgrammeModuleErrorData();
        $module_data_obj = $this->buildProgrammeModulesMock($return);
        $this->expectOutputString('no data');
        $this->module_data_task->build_programme_modules($module_data_obj, '', '', 1, 'ug', '2014');
    }
    
    public function testbuild_programme_modulesNoDataWrittenWithEmptyCluster()
    {
        $message = new stdClass();
        $message->response->rubric->cluster = array('test');
        
        $module_data_obj = $this->buildProgrammeModulesMock($message);
        $this->module_data_task->build_programme_modules($module_data_obj, '', '', 1, 'ug', '2014');
        
        $cache_key = "programme-modules.ug-2014-1";
        $output = Cache::get($cache_key);
        
        $expected = new stdClass();
        $expected->stages = array();
        
        $this->assertEquals($expected, $output);
    }
    
    public function testbuild_url_programme_modules_fullWithModuleSession2014()
    {
        $programme['pos_code'] = 'ACCF-S:BA';
        $programme['campus_id'] = '1';
        $programme['module_session'] = '2014';
        
        $url_programme_modules = Config::get('module.programme_module_base_url');
        $actual_url = $this->module_data_task->build_url_programme_modules_full($programme, $url_programme_modules, false);
        $expected_url = 'madeupurlpos=ACCF-S:BA&teachingInstitution=0122&teachingCampus=1&sessionCode=2014&format=json';
        $this->assertEquals($expected_url, $actual_url);
    }
    
    public function testbuild_url_programme_modules_fullCheckWithoutModuleSessionGives2013()
    {
        $programme['pos_code'] = 'ACCF-S:BA';
        $programme['campus_id'] = '1';
        $programme['module_session'] = '';
        
        $url_programme_modules = Config::get('module.programme_module_base_url');
        $actual_url = $this->module_data_task->build_url_programme_modules_full($programme, $url_programme_modules, false);
        $expected_url = 'madeupurlpos=ACCF-S:BA&teachingInstitution=0122&teachingCampus=1&sessionCode=2013&format=json';
        $this->assertEquals($expected_url, $actual_url);
    }
    
    public function testbuild_url_programme_modules_fullCheckNoneModuleSessionGivesEmptyUrl()
    {
        $programme['pos_code'] = 'ACCF-S:BA';
        $programme['campus_id'] = '1';
        $programme['module_session'] = 'None';
        
        $url_programme_modules = Config::get('module.programme_module_base_url');
        $actual_url = $this->module_data_task->build_url_programme_modules_full($programme, $url_programme_modules, false);
        $expected_url = '';
        $this->assertEquals($expected_url, $actual_url);
    }
    
    public function testbuild_url_programme_modules_fullWithModuleSession2014ProgrammeAsObject()
    {
        $programme = new stdClass();
        $programme->pos_code_44 = 'ACCF-S:BA';
        $programme->location_11 = '1';
        $programme->module_session_86 = '2014';
        $programme->awarding_institute_or_body_4 = '0122';
        
        $url_programme_modules = Config::get('module.programme_module_base_url');
        $actual_url = $this->module_data_task->build_url_programme_modules_full($programme, $url_programme_modules, false);
        $expected_url = 'madeupurlpos=ACCF-S:BA&teachingInstitution=0122&teachingCampus=1&sessionCode=2014&format=json';
        $this->assertEquals($expected_url, $actual_url);
    }
    
    public function testbuild_url_programme_modules_fullCheckWithoutModuleSessionGives2013ProgrammeAsObject()
    {
        $programme = new stdClass();
        $programme->pos_code_44 = 'ACCF-S:BA';
        $programme->location_11 = '1';
        $programme->module_session_86 = '';
        $programme->awarding_institute_or_body_4 = '0122';
        
        $url_programme_modules = Config::get('module.programme_module_base_url');
        $actual_url = $this->module_data_task->build_url_programme_modules_full($programme, $url_programme_modules, false);
        $expected_url = 'madeupurlpos=ACCF-S:BA&teachingInstitution=0122&teachingCampus=1&sessionCode=2013&format=json';
        $this->assertEquals($expected_url, $actual_url);
    }
    
    public function testbuild_url_programme_modules_fullCheckNoneModuleSessionGivesEmptyUrlProgrammeAsObject()
    {
        $programme = new stdClass();
        $programme->pos_code_44 = 'ACCF-S:BA';
        $programme->location_11 = '1';
        $programme->module_session_86 = 'None';
        $programme->awarding_institute_or_body_4 = '0122';
        
        $url_programme_modules = Config::get('module.programme_module_base_url');
        $actual_url = $this->module_data_task->build_url_programme_modules_full($programme, $url_programme_modules, false);
        $expected_url = '';
        $this->assertEquals($expected_url, $actual_url);
    }
    
    
    
    /**
    * helper functions
    */
    
    public function loadProgrammeModuleData()
    {
        return json_decode(file_get_contents(dirname(dirname(__FILE__)) . '/fixtures/programme-modules.json'));
    }
    
    public function loadProgrammeModuleDataFoundation()
    {
        return json_decode(file_get_contents(dirname(dirname(__FILE__)) . '/fixtures/programme-modules-foundation.json'));
    }
    
    public function loadProgrammeModuleErrorData()
    {
        $message = new stdClass();
        $message->response->message = 'no data';
        return $message;
    }
    
    public function loadModuleSynopsisData()
    {
        $module = simplexml_load_string(file_get_contents(dirname(dirname(__FILE__)) . '/fixtures/module-synopsis.xml'));
        return $module->synopsis;
    }
    
    public function loadModuleData()
    {
        return file_get_contents(dirname(dirname(__FILE__)) . '/fixtures/cached-module-data.json');
    }
    
    public function loadModuleDataFoundation()
    {
        return file_get_contents(dirname(dirname(__FILE__)) . '/fixtures/cached-module-data-foundation.json');
    }
    
    public function buildProgrammeModulesMock($return)
    {
        $module_data_obj = $this->getMock('ProgrammesPlant\ModuleData', array('get_programme_modules'));
        $module_data_obj->expects($this->once())
                        ->method('get_programme_modules')
                        ->will($this->returnValue($return));
        return $module_data_obj;
    }
    
    public function buildModuleSynopsisMock($return)
    {
        $module_data_obj = $this->getMock('ProgrammesPlant\ModuleData', array('get_module_synopsis'));
        $module_data_obj->expects($this->once())
                        ->method('get_module_synopsis')
                        ->will($this->returnValue($return));
        return $module_data_obj;
    }
    
    public function buildMock($programme_modules, $module_synopsis)
    {
        $module_data_obj = $this->getMock('ProgrammesPlant\ModuleData', array('get_programme_modules', 'get_module_synopsis'));
        
        $module_data_obj->expects($this->once())
                        ->method('get_programme_modules')
                        ->will($this->returnValue($programme_modules));
                        
        $module_data_obj->expects($this->any())
                        ->method('get_module_synopsis')
                        ->will($this->returnValue($module_synopsis));
                        
        return $module_data_obj;
    }
    
}



<?php
require_once dirname(dirname(__FILE__)) . '/lib/ControllerTestCase.php';

class TestAPI_Controller extends ControllerTestCase
{
	public $input = false;

	public function recursively_delete_directory($dir) 
	{
		$files = array_diff(scandir($dir), array('.','..'));

	    foreach ($files as $file) {
	      (is_dir("$dir/$file")) ? static::recursively_delete_directory("$dir/$file") : unlink("$dir/$file");
	    }

		return rmdir($dir);
	}

	public function populate($input)
	{
		Programme::create($input)->save();
	}

	public static function setUpBeforeClass()
	{
		Tests\Helper::migrate();

		// Remove all Programmes
		TestAPI_Controller::tearDown();
	}

	public static function tearDownAfterClass()
	{
		// Ugly way to get our .gitignore back.
		// Travis CI will likely hate this.
		exec("git checkout storage/api/.gitignore");
	}

	public function tearDown()
	{
		$programmes = Programme::all();

		foreach ($programmes as $programme)
		{
			$programme->delete();
		}

		$programme_revisions = ProgrammeRevision::all();

		foreach ($programme_revisions as $revision)
		{
			$revision->delete();
		}

		$global_settings = GlobalSetting::all();

		foreach ($global_settings as $setting)
		{
			$setting->delete();
		}

		$programme_settings = ProgrammeSetting::all();

		foreach ($programme_settings as $setting)
		{
			$setting->delete();
		}

		// We need to reset our API cache somehow.
		// This also deletes our .gitignore, which we restore after the tests are done.
		if (file_exists(path('storage') . 'api/') && is_dir(path('storage') . 'api/'))
		{
			static::recursively_delete_directory(path('storage') . 'api/');
		}

		parent::tearDown();
	}

	public function generate_programme_dependancies(){
		ProgrammeField::create(
				array(
						'field_name' => 'New field',
						'field_type' => 'textarea',
						'prefill' => 0,
						'active' => 1,
						'view' => 1
					)
			)->save();

		Award::create(
				array(
						'name' => 'Hello Award',
						'longname' => 'Long hello award'
					)
			)->save();

		Campus::create(
				array(
						'name' => 'Hello Campus'
					)
			)->save();

		Faculty::create(
				array(
						'name' => 'Hello Faculty'
					)
			)->save();

		Leaflet::create(
				array(
						'name' => 'Hello Leaflet'
					)
			)->save();

		School::create(
				array(
						'name' => 'Hello School',
						'faculties_id' => 1
					)
			)->save();

		Subject::create(
				array(
						'name' => 'Hello Subject'
					)
			)->save();

		ProgrammeSetting::create(
				array(
						'id' => 1,
						'year' => '2012'
					)
			)->save();
		$ps = ProgrammeSetting::find(1);
		$ps->makeRevisionLive($ps->get_revisions('selected')[0]);

		GlobalSetting::create(
				array(
						'id' => 1,
						'year' => '2012'
					)
			)->save();
		$gs = GlobalSetting::find(1);
		$gs->makeRevisionLive($gs->get_revisions('selected')[0]);
	}

	public function testget_indexReturnsHTTPCode204WithNoDataCached()
	{
		$response = $this->get('api@index', array('2012', 'ug'));
		$this->assertEquals('204', $response->status());
	}

	public function testget_indexReturnsHTTPCode200WithDataCached()
	{
		$this->generate_programme_dependancies();

		$input = array(
			'id' => 1, 
			Programme::get_title_field() => 'Programme 1',
			'year' => '2012'
		);

		$this->populate($input);
		$course = Programme::find(1);
		$revisions = $course->get_revisions('selected');

		if (isset($revisions[0])) {
			$course->makeRevisionLive($revisions[0]);
		}

		$response = $this->get('api@programme', array('2012', 'ug', 'programme', $input['id']));
		$this->assertEquals('200', $response->status());

		$this->markTestIncomplete();
	}

	public function testget_indexReturnsJSONWithData()
	{
		// Setup data, get response, then use $response->render() and check JSON is right.
		$this->markTestIncomplete();
	}

	public function testget_programmeReturns404WithNoCache()
	{
		$this->markTestIncomplete();
	}

	public function testget_programmeReturns200WhenCachePresent()
	{
		$this->markTestIncomplete();
	}

	public function testget_programmeReturnsJSONWhenCachePresent()
	{
		$this->markTestIncomplete();
	}

	public function testget_programmesReturnsHTTPCode204WhenOtherJSONCachesNotPresent()
	{
		$this->markTestIncomplete();
	}
}
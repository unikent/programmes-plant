<?php
require_once dirname(dirname(__FILE__)) . '/lib/ControllerTestCase.php';

class TestWebservices_Controller extends ControllerTestCase
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
		TestWebservices_Controller::tearDown();
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

		// @todo Nuke also the revisions table.

		// We need to reset our API cache somehow.
		// This also deletes our .gitignore, which we restore after the tests are done.
		if (file_exists(path('storage') . 'api/') && is_dir(path('storage') . 'api/'))
		{
			static::recursively_delete_directory(path('storage') . 'api/');
		}

		parent::tearDown();
	}

	public function testget_indexReturnsHTTPCode204WithNoDataCached()
	{
		$response = $this->get('webservices@index', array('2012', 'ug'));
		$this->assertEquals('204', $response->status());
	}

	public function testget_indexReturnsHTTPCode200WithDataCached()
	{
		$input = array(
			'id' => 1, 
			Programme::get_title_field() => 'Programme 1',
			'year' => '2012'
		);

		$this->populate($input);
		$course = Programme::find(1);
		$revisions = $course->get_revisions();
		// Set revision to live to generate a feed file - don't know how to do this!

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
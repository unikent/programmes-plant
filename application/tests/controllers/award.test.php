<?php
require_once dirname(dirname(__FILE__)) . '/lib/ControllerTestCase.php';

class TestAwards_Controller extends ControllerTestCase
{

	/**
	 * Sets up database.
	 */
	public static function setUpBeforeClass()
	{
		Tests\Helper::migrate();
	}

	/**
	 * Tests the output of get_index, which should show the index page.
	 * 
	 * @covers Awards_Controller::get_index
	 */
	public function testget_index()
	{
		$response = $this->get('awards@index');

		// Check we get the correct response
		$this->assertEquals('200', $response->foundation->getStatusCode(), 'Getting the awards index does not return a HTTP 200 code.');
	}
}
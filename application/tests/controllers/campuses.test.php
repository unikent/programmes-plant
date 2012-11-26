<?php
require_once dirname(dirname(__FILE__)) . '/lib/ControllerTestCase.php';

class TestCampuses_Controller extends ControllerTestCase
{

	/**
	 * Sets up database.
	 */
	public static function setUpBeforeClass()
	{
		Tests\Helper::migrate();
	}

}
<?php

class TestLeaflet extends PHPUnit_Framework_TestCase {
	/**
	 * Set up the database.
	 */
	public static function setUpBeforeClass()
	{
		Tests\Helper::migrate();
	}
	
}
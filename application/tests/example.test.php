<?php

class TestExample extends PHPUnit_Framework_TestCase {
	
	/**
	 * Sets up database.
	 */
	public static function setUpBeforeClass()
	{
		Tests\Helper::migrate();
	}

	/**
	 * Test that a given condition is met.
	 *
	 * @return void
	 */
	public function testSomethingIsTrue()
	{
		$this->assertTrue(true);
	}

}
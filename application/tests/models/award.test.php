<?php

class TestAward extends PHPUnit_Framework_TestCase {

	/**
	 * Set up the database.
	 */
	public static function setUpBeforeClass()
	{
		Tests\Helper::migrate();

		// Remove all elements in the awards table.
		// These are added by the Create_Intial_Awards migration.
		TestAward::tearDown();
	}

	public function tearDown()
	{
		$awards = Award::all();

		foreach ($awards as $award)
		{
			$award->delete();
		}
	}

	/**
	 * Tests all_as_list
	 * 
	 * @covers Award::all_as_list
	 */
	public function testall_as_list()
	{
		// Add in some elements to list.
		$awards = array('BA (Hons)', 'MA', 'PhD');

		foreach ($awards as $position => $name)
		{
			$award = new Award;
			$award->id = $position;
			$award->name = $name;
			$award->save();
		}

		$this->assertEquals($awards, Award::all_as_list(), "Award::all_as_list did not return the same as was added to the database.");

		// Remove an award from the database
		$a = Award::first();
		$a->delete();

		// Remove first award from the array
		unset($awards[0]);

		$this->assertEquals($awards, Award::all_as_list(), "Award::all_as_list did not return the same as was added to the database when we removed an award.");
	}

}
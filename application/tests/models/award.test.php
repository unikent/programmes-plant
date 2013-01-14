<?php

class TestAward extends ModelTestCase 
{

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

		parent::tearDown();
	}

	/**
	 * Populates the database with inputted array.
	 * 
	 * @param array $input An array of items to add the database.
	 * @return void
	 */
	public function populate($input)
	{
		foreach ($input as $position => $name)
		{
			$award = Award::create(array('id' => $position, 'name' => $name))->save();
		}
	}

	public function testall_as_list()
	{
		// Add in some elements to list.
		$awards = array('BA (Hons)', 'MA', 'PhD');
		$this->populate($awards);

		$this->assertEquals($awards, Award::all_as_list(), "Award::all_as_list did not return the same as was added to the database.");

		// Remove an award from the database
		$a = Award::first();
		$a->delete();

		//Wipe memory cache
		Award::$list_cache = false;

		$this->assertEquals($awards, Award::all_as_list(), "Award::all_as_list did not return the same as was added to the database when we removed an award.");
	}
	
}
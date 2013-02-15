<?php

class TestAward extends ModelTestCase 
{

	public $run = false;
	
	/**
	 * Set up the database.
	 */
	public static function setUpBeforeClass()
	{
		Tests\Helper::migrate();
	}

	public function tearDown()
	{
		$awards = Award::all();

		foreach ($awards as $award)
		{
			$award->delete_for_test();
		}

		parent::tearDown();
	}

	public function setUp()
	{
		if (! $this->run)
		{
			$this->tearDown();
		}
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
		$a->delete_for_test();

		//Wipe memory cache
		Award::$list_cache = false;

		$this->assertEquals($awards, Award::all_as_list(), "Award::all_as_list did not return the same as was added to the database when we removed an award.");
	}
	
}
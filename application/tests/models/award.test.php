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
		static::tear_down();
	}

	public static function tear_down()
	{
		$awards = UG_Award::all();

		foreach ($awards as $award)
		{
			$award->delete_for_test();
		}

		parent::tear_down();
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
			$award = UG_Award::create(array('id' => $position, 'name' => $name))->save();
		}
	}

	public function testall_as_list()
	{
		// Add in some elements to list.
		$awards = array('BA (Hons)', 'MA', 'PhD');
		$this->populate($awards);

		$this->assertEquals($awards, UG_Award::all_as_list(), "Award::all_as_list did not return the same as was added to the database.");

		// Remove an award from the database
		$a = UG_Award::first();
		$a->delete_for_test();

		//Wipe memory cache
		UG_Award::$list_cache = false;

		$this->assertEquals($awards, UG_Award::all_as_list(), "Award::all_as_list did not return the same as was added to the database when we removed an award.");
	}
	
}
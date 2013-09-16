<?php

class TestAwards_Controller extends ControllerTestCase
{

	/**
	 * Sets up database.
	 */
	public static function setUpBeforeClass()
	{
		Tests\Helper::migrate();
		Auth::login(1);
		// Remove all elements in the awards table.
		// These are added by the Create_Intial_Awards migration.
		TestAward::tear_down();
	}

	/**
	 * Remove all the awards.
	 */
	public static function tear_down()
	{
		$awards = UG_Award::all();

		foreach ($awards as $award)
		{
			$award->delete_for_test();
		}
	}

	/**
	 * Populate the awards database with an array.
	 * 
	 * @param array $awards An array of awards to add.
	 */
	private function populate($awards)
	{
		foreach ($awards as $position => $name)
		{
			$award = new UG_Award;
			$award->id = $position;
			$award->name = $name;
			$award->hidden = 0;
			$award->save();
		}
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

		// Add some awards to the database
		$input = array(1 => 'BA', 2=> 'MA', 3=> 'PhD');
		$this->populate($input);

		$data = $this->get_data('awards@index');

		// Flatten awards
		$returned_array = Array();

		foreach ($data['items'] as $award)
		{
			$returned_array[] = $award->name;
		}

		foreach ($input as $award)
		{
			$this->assertTrue(in_array($award, $returned_array), "Awards index did not return inputted awards.");
		}
		
	}

	/**
	 * Tests output of get_create.
	 *
	 * @covers Awards_Controller::get_create
	 */
	public function testget_edit()
	{
		$input = array(1 => 'BA', 2 => 'MA', 3 => 'PhD');
		$this->populate($input);

		$response = $this->get('awards@edit', array('1'));

		// Check we get the correct response
		$this->assertEquals('200', $response->foundation->getStatusCode(), 'Getting the awards edit page does not return a HTTP 200 code.');

		// Check that we get out what we've put in when we request a view.
		$count = 1;
		while ($count <= 3)
		{
			$data = $this->get_data('awards@edit', array($count));

			$this->assertEquals($input[$count], $data['item']->name);

			$count++;
		}
	}

	/**
	 * Tests output of get_create.
	 *
	 * @covers Awards_Controller::get_create
	 */
	public function testget_create()
	{
		$response = $this->get('awards@create');

		// Check we get the correct response
		$this->assertEquals('200', $response->foundation->getStatusCode(), 'Getting the awards creation page does not return a HTTP 200 code.');
	}
}
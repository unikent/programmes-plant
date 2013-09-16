<?php

class TestGlobalSettingFields_Controller extends ControllerTestCase
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
		static::tear_down();
	}

	/**
	 * Remove all the fields.
	 */
	public static function tear_down()
	{
		$globalsettingfields = GlobalSettingField::all();

		foreach ($globalsettingfields as $globalsettingfield)
		{
			$globalsettingfield->delete();
		}
	}

	/**
	 * Populate the globalsettingfields table
	 * 
	 */
	private function populate($input)
	{
		$globalsettingfield = new GlobalSettingField;
		$globalsettingfield->id = $input['id'];
		$globalsettingfield->field_name = $input['name'];
		$globalsettingfield->field_type = $input['type'];
		$globalsettingfield->field_description = $input['description'];
		$globalsettingfield->active = 1;
		$globalsettingfield->view = 1;
		$globalsettingfield->save();
	}
	/**
	 * Tests the output of get_index, which should show the index page.
	 * 
	 * @covers TestGlobalSettingFields_Controller::get_index
	 */
	public function testget_index()
	{

		// Add a field to the database
		$input = array('id' => 1, 'name' => 'Test global field', 'type' => 'text', 'description' => 'A test global setting');
		$this->populate($input);

		$response = $this->get('globalsettingfields@index');

		// Check we get the correct response
		$this->assertEquals('200', $response->foundation->getStatusCode(), 'Getting the awards index does not return a HTTP 200 code.');

		$data = $this->get_data('globalsettingfields@index');

		$returned_array = array();
		foreach ($data['fields'] as $globalsettingfield)
		{
			$returned_array[] = $globalsettingfield->field_name;
		}
		$this->assertTrue(in_array('Test global field', $returned_array), "Global setting fields index did not return inputted fields.");
		
	}

	/**
	 * Tests output of get_add
	 *
	 * @covers TestGlobalSettingFields_Controller::get_add
	 */
	public function testget_edit()
	{
		
		// Add a field to the database
		$input = array('id' => 2, 'name' => 'Another test global field', 'type' => 'text', 'description' => 'Another test global setting');
		$this->populate($input);

		$response = $this->get('globalsettingfields@edit', array(2));

		// Check we get the correct response
		$this->assertEquals('200', $response->foundation->getStatusCode(), 'Getting the awards index does not return a HTTP 200 code.');

		$data = $this->get_data('globalsettingfields@edit', array(2));

		$this->assertEquals('Another test global field', $data['values']->field_name);
	}

	/**
	 * Tests output of get_add
	 *
	 * @covers TestGlobalSettingFields_Controller::get_add
	 */
	public function testget_create()
	{
		$response = $this->get('globalsettingfields@add');

		// Check we get the correct response
		$this->assertEquals('200', $response->foundation->getStatusCode(), 'Getting the awards creation page does not return a HTTP 200 code.');
	}
}
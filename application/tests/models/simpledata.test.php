<?php

class Thing extends SimpleData {}

class TestSimpleData extends ModelTestCase
{
	public function tearDown()
	{
		Thing::$rules = array();

		if (! is_null(Thing::$validation))
		{
			Thing::$validation = null;
		}

		parent::tearDown();
	}
	
	/**
	* test that all_as_list for an example class returns the correct empty drop-down, where $empty_default_value (which would be from the db) is set to 1
	*/
	public function testall_as_listReturnsEmptyDefault()
	{
	    Award::$list_cache = false;
	    $empty_default_value = 1;
    	$options = Award::all_as_list($empty_default_value);
    	$this->assertNotNull($options[0]);
    	$this->assertEquals(__('fields.empty_default_value'), $options[0]);
	}
	
	/**
	* test that all_as_list for an example class returns the correct empty drop-down, where $empty_default_value (which would be from the db) is set to 0
	*/
	public function testall_as_listReturnsNoDefault()
	{
	    Award::$list_cache = false;
	    $empty_default_value = 0;
    	$options = Award::all_as_list($empty_default_value);
    	$this->assertArrayNotHasKey(0, $options);
	}

	public function testall_as_listReturnsArray() {}

	public function testall_as_listReturnsData() {}

	public function testis_validRulesAreAddedSuccessfully() 
	{
		Thing::$rules = array('id' => 'required');

		Thing::is_valid(array('name' => 'required'));

		$this->assertCount(2, Thing::$rules);
	}

	public function testis_validRulesAreOverwrittenSuccessfully()
	{
		Thing::$rules = array('id' => 'required');

		Thing::is_valid(array('id' => 'max:255'));

		$this->assertEquals('max:255', Thing::$rules['id']);
	}
	
	public function testis_validReturnsTrueWhenValidationSucceeds() 
	{
		Thing::$rules = array('email' => 'required|email', 'address' => 'required|url');

		Request::foundation()->request->add(array('email' => 'alex@example.com', 'address' => 'http://example.com'));

		$this->assertTrue(Thing::is_valid());
	}

	public function testis_validReturnsFalseWhenValidationFails()
	{
		Thing::$rules = array('id' => 'required');

		Request::foundation()->request->add(array('id' => null));

		$this->assertFalse(Thing::is_valid());
	}

	public function testpopulate_from_inputSetsModelFromInput() 
	{
		$input = array('email' => 'alex@example.com', 'address' => 'http://example.com');
		Request::foundation()->request->add($input);

		$thing = new Thing;

		$thing->is_valid();
		$thing->populate_from_input();

		$this->assertEquals($input, $thing->attributes);
	}

	/**
     * @expectedException NoValidationException
     */
	public function testpopulate_from_inputThrowsExceptionThereIsNoValidation() 
	{
		$input = array('email' => 'alex@example.com', 'address' => 'http://example.com');
		Request::foundation()->request->add($input);

		$thing = new Thing;
		$thing->populate_from_input();
	}

}
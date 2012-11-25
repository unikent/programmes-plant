<?php

class Thing extends SimpleData {}

class TestSimpleData extends PHPUnit_Framework_TestCase
{
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
		Thing::$rules = array('id' => 'required', );

		Request::foundation()->request->add(array('id' => null));

		$this->assertFalse(Thing::is_valid());
	}

	public function testpopulate_from_inputSetsModelFromInput() {}

	public function testpopulate_from_inputWarnsWhenThereIsNoValidation() {}

}
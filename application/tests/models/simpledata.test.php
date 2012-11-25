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
	
	public function testis_validReturnsTrueWhenValidationSucceeds() {}

	public function testis_validReturnsFalseWhenValidationFails() {}

	public function testpopulate_from_inputSetsModelFromInput() {}

	public function testpopulate_from_inputWarnsWhenThereIsNoValidation() {}

}
<?php

class Thing extends SimpleData {}

class TestSimpleData extends PHPUnit_Framework_TestCase
{
	public function testall_as_listReturnsArray() {}

	public function testall_as_listReturnsData() {}

	public function testis_validRulesAreAddedSuccessfully() {}

	public function testis_validReturnsTrueWhenValidationSucceeds() {}

	public function testis_validReturnsFalseWhenValidationFails() {}

	public function testpopulate_from_inputSetsModelFromInput() {}

	public function testpopulate_from_inputWarnsWhenThereIsNoValidation() {}

}
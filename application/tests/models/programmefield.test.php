<?php

class TestProgrammeField extends PHPUnit_Framework_TestCase {

	/**
	 * Set up the database.
	 */
	public static function setUpBeforeClass()
	{
		Tests\Helper::migrate();

		// Remove all elements in the awards table.
		// These are added by the Create_Intial_Awards migration.
		//TestProgrammeField::tearDown();
	}

	public function tearDown()
	{
		$programme_fields = ProgrammeField::all();

		foreach ($programme_fields as $programme_field)
		{
			$programme_field->delete();
		}
	}

	/**
	 * Tests getAsList
	 * 
	 * @covers Award::getAsList
	 */
	public function testReorderFields()
	{
		// do the reordering
		$order_string = 'field-id-3,field-id-2,field-id-1';
		ProgrammeField::reorder($order_string);
		
		// pull out id-1
		$programme_field = ProgrammeField::find(1);
		
		// check if the order of id-1 is now set to 3 (as per our order_string)
		$this->assertEquals($programme_field->order, '3', "ProgrammeField::reorder did not return the correct ordering.");
		
		
		// do some more reordering
		$order_string = 'field-id-2,field-id-1,field-id-3';
		ProgrammeField::reorder($order_string);
		
		// pull out id-1 again
		$programme_field = ProgrammeField::find(1);
		
		// check if the order of id-1 has now changed to 2
		$this->assertEquals($programme_field->order, '2', "ProgrammeField::reorder did not return the correct ordering.");
	}

}
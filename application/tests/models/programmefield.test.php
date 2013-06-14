<?php

class TestProgrammeField extends ModelTestCase {

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

	}

	/**
	 * Tests Reorder fields
	 * 
	 * @covers Model:reorder()
	 */
	public function testReorderFields()
	{
		// do the reordering
		$order_string = 'field-id-3,field-id-2,field-id-1';
		UG_ProgrammeField::reorder($order_string, 1);
		
		// pull out id-1
		$programme_field = UG_ProgrammeField::find(1);
		
		// check if the order of id-1 is now set to 3 (as per our order_string)
		$this->assertEquals($programme_field->order, '3', "UG_ProgrammeField::reorder did not return the correct ordering.");
		
		
		// do some more reordering
		$order_string = 'field-id-2,field-id-1,field-id-3';
		UG_ProgrammeField::reorder($order_string, 1);
		
		// pull out id-1 again
		$programme_field = UG_ProgrammeField::find(1);
		
		// check if the order of id-1 has now changed to 2
		$this->assertEquals($programme_field->order, '2', "UG_ProgrammeField::reorder did not return the correct ordering.");
	}
	
	
	/**
	* Tests that programme fields are returned in the expected section and ordering
	* 
	* while testReorderFields() tests that ordering is correctly set for a specific field, this test makes sure the array of all sections and fields is correct. This ensures that the programme edit/create page displays the fields in the correct order
	*
	* @covers UG_ProgrammeField::programme_fields_by_section()
	*/
	public function testProgrammeFieldsBySection()
	{
    	// reorder the first 3 fields in section 1 so they're id3, id2, then id1
		$order_string = 'field-id-3,field-id-2,field-id-1';
		UG_ProgrammeField::reorder($order_string, 1);
		
		// get the programme fields
		$programme_fields = UG_ProgrammeField::programme_fields_by_section();
		
		// test that 'Programme title and key facts' is the first section name
		$keys = array_keys($programme_fields);
		$this->assertEquals('Programme title and key facts', $keys[0]);
		
		// check that field #1 has id 3, field #2 has id 2, and field #3 has id 1
		$this->assertEquals(3, $programme_fields['Programme title and key facts'][1]->id);
		$this->assertEquals(2, $programme_fields['Programme title and key facts'][2]->id);
		$this->assertEquals(1, $programme_fields['Programme title and key facts'][3]->id);
				
	}

	public function testAssignFieldsShouldModifyTitleForStandardUser()
	{

		$user = Tests\TestUser::create_with_permissions(array('ug_fields_read_programme_title_1', 'ug_fields_write_programme_title_1'));

		// create a programme
		UG_Programme::create(array('programme_title_1' => 'Thing', 'year'=> '2014' , 'created_by' => "test user"));
		$programme = UG_Programme::find(1);
		$programme_fields = UG_ProgrammeField::programme_fields();
		
		$programme_obj = UG_ProgrammeField::assign_fields($programme, $programme_fields, array('programme_title_1' => 'Thing 2'), $user);

		$this->assertEquals($programme_obj->programme_title_1, 'Thing 2');
	}
    
}
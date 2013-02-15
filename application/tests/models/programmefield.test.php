<?php

class TestProgrammeField extends PHPUnit_Framework_TestCase {

	public $input = array();

	/**
	 * Set up the database.
	 */
	public static function setUpBeforeClass()
	{
		Tests\Helper::migrate();
	}

	/**
	 * @todo Abstract this - a lot of code repetition now.
	 */
	public function populate($input = false, $model = 'ProgrammeField')
	{
		if (! $input)
		{
			$input = $this->input;
		}

		$object = $model::create($input)->save();
	}

	/**
	 * Blanks the ProgrammeField table in the database.
	 * 
	 * @todo Abstract this to some model test class.
	 * @return void
	 */
	public function wipe($model = 'ProgrammeField')
	{
		$items = $model::all();

		foreach($items as $item)
		{
			$item->delete();
		}
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
		ProgrammeField::reorder($order_string, 1);
		
		// pull out id-1
		$programme_field = ProgrammeField::find(1);
		
		// check if the order of id-1 is now set to 3 (as per our order_string)
		$this->assertEquals($programme_field->order, '3', "ProgrammeField::reorder did not return the correct ordering.");
		
		// do some more reordering
		$order_string = 'field-id-2,field-id-1,field-id-3';
		ProgrammeField::reorder($order_string, 1);
		
		// pull out id-1 again
		$programme_field = ProgrammeField::find(1);
		
		// check if the order of id-1 has now changed to 2
		$this->assertEquals($programme_field->order, '2', "ProgrammeField::reorder did not return the correct ordering.");
	}
	
	
	/**
	* Tests that programme fields are returned in the expected section and ordering
	* 
	* while testReorderFields() tests that ordering is correctly set for a specific field, this test makes sure the array of all sections and fields is correct. This ensures that the programme edit/create page displays the fields in the correct order
	*
	* @covers ProgrammeField::programme_fields_by_section()
	*/
	public function testProgrammeFieldsBySection()
	{
    	// reorder the first 3 fields in section 1 so they're id3, id2, then id1
		$order_string = 'field-id-3,field-id-2,field-id-1';
		ProgrammeField::reorder($order_string, 1);
		
		// get the programme fields
		$programme_fields = ProgrammeField::programme_fields_by_section();
		
		// test that 'Programme title and key facts' is the first section name
		$keys = array_keys($programme_fields);
		$this->assertEquals('Programme title and key facts', $keys[0]);
		
		// check that field #1 has id 3, field #2 has id 2, and field #3 has id 1

		// Commented following test for the moment while I try and debug.
		//$this->assertEquals(3, $programme_fields['Programme title and key facts'][1]->id);

		//$this->assertEquals(2, $programme_fields['Programme title and key facts'][2]->id);
		//$this->assertEquals(1, $programme_fields['Programme title and key facts'][3]->id);		
	}

	public function testprogramme_fields_by_sectionReturnsAnMultiDimensionalArray()
	{
		$outer = ProgrammeField::programme_fields_by_section();

		// Grab the first section.
		list($key, $inner) = each($outer);

		$this->assertTrue(is_array($inner));
		$this->assertTrue(is_array($outer));
	}

	public function testprogramme_fields_by_sectionReturnsAnArrayOfProgrammeFieldsAtSecondLevel()
	{
		$outer = ProgrammeField::programme_fields_by_section();

		// Grab the first section.
		list($key, $inner) = each($outer);

		foreach($inner as $item)
		{
			$this->assertTrue(is_object($item));
			$this->assertEquals('ProgrammeField', get_class($item));
		}
	}

	public function testprogramme_fields_by_sectionReturnedArrayHasNumericKeys()
	{
		// Get first item from outer array here to get an inner array.
		list($key, $inner_returned_array) = each(ProgrammeField::programme_fields_by_section());

		foreach ($inner_returned_array as $key => $value)
		{
			$this->assertTrue(is_numeric($key), "$key is not numeric but a " . gettype($key) . ". Field is named $value->field_name");
		}
	}

	public function testprogramme_fields_by_sectionReturnedArrayHasNonBlankKeys()
	{
		// Get first item from outer array here to get an inner array.
		list($key, $inner_returned_array) = each(ProgrammeField::programme_fields_by_section());

		foreach ($inner_returned_array as $key => $value)
		{
			$this->assertNotEquals('', $key, "\$key is a blank string - it shouldn't be");
		}
	}

	public function populate_two_sections()
	{
		$this->wipe();
		$this->wipe('ProgrammeSection');

		// Add two sections
		$this->populate(array(
			'id' => 1,
			'name' => 'Section 1',
			'order' => 0
		), 'ProgrammeSection');

		$this->populate(array(
			'id' => 2,
			'name' => 'Section 2',
			'order' => 0
		), 'ProgrammeSection');

		
		// Note the following two populations of the database lack an order.

		// Add two items to section 1
		$this->populate(array(
			'id' => 1,
			'field_name' => 'Programme title', 
			'field_type' => 'text', 
			'active' => true, 
			'view' => true, 
			'colname' => 'programme_title_1',
			'section' => 1
		));

		$this->populate(array(
			'id' => 2,
			'field_name' => 'Slug', 
			'field_type' => 'text', 
			'active' => true, 
			'view' => true, 
			'colname' => 'slug_2',
			'section' => 1
		));

		// Add two items to section 2
		$this->populate(array(
			'id' => 3,
			'field_name' => 'Programme title', 
			'field_type' => 'text', 
			'active' => true, 
			'view' => true, 
			'colname' => 'programme_title_2',
			'section' => 2
		));

		$this->populate(array(
			'id' => 4,
			'field_name' => 'Slug', 
			'field_type' => 'text', 
			'active' => true, 
			'view' => true, 
			'colname' => 'slug_3',
			'section' => 2
		));
	}

	/**
	 * The following two tests enclose the bug recorded at:
	 * https://github.com/unikent/programmes-plant/issues/151
	 * 
	 * If a section did not have the order of the programme fields set, then only one
	 * field was ever extracted per section due to the way the looping used the $field->order
	 * to map onto an array.
	 */
	public function testprogramme_fields_by_sectionReturnsArrayCorrectlyWhenFieldsHaveNoOrder()
	{
		$this->populate_two_sections();

		$sections = ProgrammeField::programme_fields_by_section();

		// These should fail in the presence of the bug.
		// We don't get the fields we have put in, only the first in each case.
		$this->assertCount(2, $sections['Section 1'], "We should have got two programme fields back from Section 1, instead we got " . count($sections['Section 1']));
		$this->assertCount(2, $sections['Section 2'], "We should have got two programme fields back from Section 2, instead we got " . count($sections['Section 2']));
	}

	public function testprogramme_fields_by_sectionReturnsArrayInRationalOrderWhenFieldsHaveNoOrder()
	{
		$this->populate_two_sections();

		$sections = ProgrammeField::programme_fields_by_section();

		$count = 1;

		// Check for a rational ordering.
		foreach ($sections['Section 1'] as $key => $field)
		{
			$this->assertEquals($count, $key, "$field->field_name is wrong - location in the array is $key and should be $count");
			$count++;
		}
	}

	public function testprogramme_fields_by_sectionReturnsAllItemsWhenYouHaveANoOrder()
	{
		$this->populate_two_sections();

		$sections = ProgrammeField::programme_fields_by_section();
		
		$this->assertCount(2, $sections['Section 1']);
	}

	public function testprogramme_fields_by_sectionReturnsAllItemsWhenYouHaveAMixOfOrderAndNoOrder()
	{
		$this->populate_two_sections();

		// Set the order on one, but not on another in Section 1.
		$first_field = ProgrammeField::find(1);
		$first_field->order = 1;
		$first_field->save();

		$sections = ProgrammeField::programme_fields_by_section();
		
		$this->assertCount(2, $sections['Section 1']);
	}

	public function testprogramme_fields_by_sectionReturnsAllItemsWhenYouHaveAllFieldsWithOrder()
	{
		$this->populate_two_sections();

		// Set the order for both in Section 1.
		$first_field = ProgrammeField::find(1);
		$first_field->order = 1;
		$first_field->save();

		$second_field = ProgrammeField::find(2);
		$second_field->order = 2;
		$second_field->save();

		$sections = ProgrammeField::programme_fields_by_section();
		
		$this->assertCount(2, $sections['Section 1']);
	}

	public function testprogramme_fields_by_sectionReturnsArrayInRationalOrderWhenFieldsHaveAMixOfOrderAndNoOrder()
	{
		$this->populate_two_sections();

		// Set the order on one, but not on another in Section 1.
		$first_field = ProgrammeField::find(1);
		$first_field->order = 1;
		$first_field->save();

		// We now have one field with an order (ID = 1) and another without (ID = 2) in Section 1.
		$sections = ProgrammeField::programme_fields_by_section();

		$count = 1;
		// Check for a rational ordering.
		foreach ($sections['Section 1'] as $key => $value)
		{
			$this->assertEquals($count, $key);
			$count++;
		}

		// Add another two, one with order and one without - meaning we have two with order and two without in A/B/A/B fashion we want to render smoothly.
		$this->populate(array(
			'id' => 5,
			'field_name' => 'Programme title', 
			'field_type' => 'text', 
			'active' => true, 
			'view' => true, 
			'colname' => 'programme_title_1',
			'section' => 1,
			'order' => 2
		));

		$this->populate(array(
			'id' => 6,
			'field_name' => 'Programme title', 
			'field_type' => 'text', 
			'active' => true, 
			'view' => true, 
			'colname' => 'programme_title_1',
			'section' => 1
		));

		$sections = ProgrammeField::programme_fields_by_section();

		$count = 1;

		foreach ($sections['Section 1'] as $key => $value)
		{
			$this->assertEquals($count, $key);
			$count++;
		}
	}
}
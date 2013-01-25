<?php

class TestSchool extends ModelTestCase
{

	/**
	 * Set up the database.
	 */
	public static function setUpBeforeClass()
	{
		Tests\Helper::migrate();

		$faculty = new Faculty;
		$faculty->save();
	}

	/**
	 * Remove everything in the database.
	 */
	public function tearDown()
	{
		$schools = School::all();

		foreach ($schools as $school)
		{
			$school->delete_for_test();
		}

		parent::tearDown();
	}

	public function testInputPopulatesSchoolObjectSuccessfully()
	{
		$input = array('name' => 'Blah', 'faculty' => 1);
		Request::foundation()->request->add($input);

		$school = new School;
		$school->input();

		// We should be saving $_POST['faculty'] to $school->faculties_id
		$input['faculties_id'] = $input['faculty'];
		unset($input['faculty']);
		
		$this->assertEquals($input, $school->attributes);
	}

	public function testSchoolObjectSavesSuccessfully()
	{
		$input = array('name' => 'A School', 'faculties_id' => 1);

		$school = new School;
		foreach($input as $key => $value){ $school->$key = $value; }
		$school->save();

		$return = DB::table('schools')
				  ->where(function($query) use ($input)
				  {
				  	$query->where('name', '=', $input['name']);
				  	$query->where('faculties_id', '=', $input['faculties_id']);
				  })
				  ->get();
		unset($GLOBALS['input']);

		$this->assertNotEmpty($return, "We didn't write our school to the database - it wasn't there when we tried to get it back.");
		$this->assertCount(1, $return, "We got more than one school back from the database.");

		foreach($input as $key => $value)
		{
			$this->assertEquals($value, $return[0]->$key, "Inputted $key => $value came back wrongly.");
		}
	}

	public function testValidatesSuccessfullyWhenEverythingIsRight() 
	{
		Request::foundation()->request->add(array('name' => 'blah', 'faculty' => 1));

		$this->assertTrue(School::is_valid());
	}

	/**
     * @expectedException Laravel\Database\Exception
     */
	public function testThrowExceptionWhenWeTryAndAddSomethingWackyInSchoolToDatabase()
	{
		$input = array(
			'name' => 'School',
			'faculties_id' => 1,
			'nonsense' => 'Rubbish'
		);

		$leaflet = new Leaflet;
		foreach($input as $key => $value) { $leaflet->$key = $value; }
		$leaflet->save();
	}

	public function testFailsToValidateWhenNameIsNotPresent() 
	{
		Request::foundation()->request->add(array('name' => null, 'faculty' => 1));

		$this->assertFalse(School::is_valid());
	}

	public function testFailsToValidateWhenNameIsLongerThan255Characters() 
	{
		Request::foundation()->request->add(array('name' => str_pad('', 260, 'a'), 'faculty' => 1));

		$this->assertFalse(School::is_valid());
	}

	public function testFailsToValidateWhenNameIsNotUnique()
	{
		$school = new School;
		$school->name = 'Fun';
		$school->save();

		Request::foundation()->request->add(array('name' => 'Fun', 'faculty' => 1));

		$this->assertFalse(School::is_valid());
	}

	public function testFailsToValidateWhenFacultyIsNotPresent() 
	{
		Request::foundation()->request->add(array('name' => 'Blah', 'faculty' => null));

		$this->assertFalse(School::is_valid());
	}

	public function testFailsToValidateWhenFacultyDoesNotExist()
	{
		Request::foundation()->request->add(array('name' => 'Blah', 'faculty' => 200));

		$this->assertFalse(School::is_valid());
	}
}
<?php
// @todo Do we want a test double for Input::all()?
// @todo Do we want to test double Campus? As we otherwise may not be testing in isolaton.
// A good way of doing this is to seperate our database handling bits of the model from others.
class TestLeaflet extends ModelTestCase 
{

	/**
	 * Set up the database.
	 */
	public static function setUpBeforeClass()
	{	
		static::setURI('ug','2014');
		Tests\Helper::migrate();

		// Chuck all campuses to ensure clean database
		$campuses = Campus::all();

		foreach ($campuses as $campus)
		{
			$campus->delete_for_test();
		}

		parent::tear_down();
	}

	/**
	 * Remove everything in the database.
	 */
	public static function tear_down()
	{
		$leaflets = UG_Leaflet::all();

		foreach ($leaflets as $leaflet)
		{
			$leaflet->delete_for_test();
		}

		// Chuck all campuses to ensure clean database
		$campuses = Campus::all();

		foreach ($campuses as $campus)
		{
			$campus->delete_for_test();
		}
		
		parent::tear_down();
	}

	/**
	 * We always want the ID here to be 1 - we only ever have a campus with this ID.
	 */
	public function setUp()
	{
		$campus = new Campus;
		$campus->id = 1;
		$campus->save();
	}

	public function testCreateNewLeaflet()
	{
		$input = array(
			'name' => 'Some Leaflet',
			'campuses_id' => 1,
			'tracking_code' => 'http://example.com/tracking'
		);

		$leaflet = new UG_Leaflet;

		foreach($input as $key => $value)
		{
			$leaflet->$key = $value;
		}
		$leaflet->save();

		$return = DB::table('leaflets_ug')
				  ->where(function($query) use ($input)
				  {
				  	$query->where('name', '=', $input['name']);
				  	$query->where('campuses_id', '=', $input['campuses_id']);
				  	$query->where('tracking_code', '=', $input['tracking_code']);
				  })
				  ->get();
		unset($GLOBALS['input']);

		$this->assertNotEmpty($return, "We didn't write our leaflet to the database - it wasn't there when we tried to get it back.");
		$this->assertCount(1, $return, "We got more than one leaflet back from the database.");

		foreach($input as $key => $value)
		{
			$this->assertEquals($value, $return[0]->$key, "Inputted $key => $value came back wrongly.");
		}
	}

	/**
     * @expectedException Laravel\Database\Exception
     */
	public function testThrowExceptionWhenWeTryAndAddSomethingWackyInLeafletToDatabase()
	{
		$input = array(
			'name' => 'Some Leaflet',
			'campuses_id' => 1,
			'tracking_code' => 'http://example.com/tracking',
			'nonsense' => 'Rubbish'
		);

		$leaflet = new UG_Leaflet;
		foreach($input as $key => $value) { $leaflet->$key = $value; }
		$leaflet->save();
	}

	public function testValidatesOnSuccessfulInput()
	{
		$input = array(
			'name' => 'Some Leaflet',
			'campus' => 1,
			'tracking_code' => 'http://example.com/tracking'
		);

		Request::foundation()->request->add($input);

		$this->assertTrue(UG_Leaflet::is_valid());
	}

	public function testFailsToValidateWhenNoNameIsPresent() 
	{
		$input = array(
			'name' => null,
			'campus' => 1,
			'tracking_code' => 'http://example.com/tracking'
		);

		Request::foundation()->request->add($input);

		$this->assertFalse(UG_Leaflet::is_valid());
	}

	public function testFailsToValidateWhenNameIsNotUnique() 
	{
		$leaflet = new UG_Leaflet;
		$leaflet->name = 'Some Leaflet';
		$leaflet->save();

		$input = array(
			'name' => 'Some Leaflet',
			'campus' => 1,
			'tracking_code' => 'http://example.com/tracking'
		);

		Request::foundation()->request->add($input);

		$this->assertFalse(UG_Leaflet::is_valid());
	}

	public function testFailsToValidateWhenNameIsLongerThan255Characters()
	{
		$input = array(
			'name' => str_pad('', 260, 'a'),
			'campus' => 1,
			'tracking_code' => 'http://example.com/tracking'
		);

		Request::foundation()->request->add($input);

		$this->assertFalse(UG_Leaflet::is_valid());
	}

	public function testFailsToValidateWhenCampusCodeIsNotPresent()
	{
		$input = array(
			'name' => 'Some Leaflet',
			'campus' => null,
			'tracking_code' => 'http://example.com/tracking'
		);

		Request::foundation()->request->add($input);

		$this->assertFalse(UG_Leaflet::is_valid());
	}
	
	public function testFailsToValidateWhenCampusCodeDoesNotExist() 
	{
		$input = array(
			'name' => 'Some Leaflet',
			'campus' => 2,
			'tracking_code' => 'http://example.com/tracking'
		);

		Request::foundation()->request->add($input);

		$this->assertFalse(UG_Leaflet::is_valid());
	}

	public function testFailsToValidateOnTrackingCodeIsNotPresent()
	{
		$input = array(
			'name' => 'Some Leaflet',
			'campus' => 1,
			'tracking_code' => null
		);

		Request::foundation()->request->add($input);

		$this->assertFalse(UG_Leaflet::is_valid());
	}

	/**
	 * We could feed this lots of URLs, but this would be testing
	 * the URL Laravel filter, not simply that it is somehow used here.
	 */
	public function testFailsToValidateWhenCampusCodeIsNotAURL()
	{
		$input = array(
			'name' => 'Some Leaflet',
			'campus' => 1,
			'tracking_code' => 'not a url'
		);

		Request::foundation()->request->add($input);

		$this->assertFalse(UG_Leaflet::is_valid());
	}

}
<?php

class TestCampus extends ModelTestCase
{
	// The minimum input required to get it through the validation.
	var $input = array();

	/**
	 * Set up the database.
	 */
	public static function setUpBeforeClass()
	{
		Tests\Helper::migrate();
	}

	/**
	 * Remove everything in the database.
	 */
	public static function tear_down()
	{

		static::clear_models(array("campus"));
		
		parent::tear_down();
	}

	public function setUp()
	{
		$this->input = array(
			'name' => 'A campus',
			'address_1' => '1',
			'address_2' => '2',
			'address_3' => '3',
			'phone' => '82828',
			'postcode' => 'NG4 3TG'
		);
	}

	/**
	 * Simple check that our data made it to the database.
	 * 
	 * @param string $field The column, thefirst half of a SQL where statement ran against the database.
	 * @param string $value The value of the column, the second half of a SQL where statement against the database.
 	 * @return bool True if we have this in our database.
	 */
	public function check_for_data($field, $value)
	{
		$return = DB::table('campuses')
				  ->where($field, '=', $value)
				  ->get();

		if (count($return) == 0)
		{
			return false;
		}

		if (count($return) > 1)
		{
			return false;
		}

		return true;
	}

	public function testValidCampusValidatesSuccessfully()
	{
		$all_correct = array(
			'name' => 'A Campus',
			'address_1' => '1',
			'address_2' => '2',
			'address_3' => '3',
			'phone' => '82828',
			'postcode' => 'NG4 3TG',
			'phone' => '01567 876234',
			'fax' => '01567 873434',
			'town' => 'Wolverhampton',
			'email' => 'alex@example.com',
			'postcode' => 'WR5 4GH',
			'url' => 'http://example.com'
		);

		Request::foundation()->request->add($all_correct);

		$this->assertTrue(Campus::is_valid());
	}

	public function testNameIsRequired() 
	{

		unset($this->input['name']);

		Request::foundation()->request->add($this->input);

		$this->assertFalse(Campus::is_valid());
	}

	public function testNameIsUnique() 
	{
		// A campus is used as the name in $this->input
		$campus = Campus::create(array('name' => 'A campus'));
		$campus->save();

		Request::foundation()->request->add($this->input);

		$this->assertFalse(Campus::is_valid());
	}

	public function testNameIsLessThan255Characters()
	{
		$this->input['name'] = str_pad('', 260, 'a');

		Request::foundation()->request->add($this->input);

		$this->assertFalse(Campus::is_valid());
	}

	public function testAddDescription() 
	{
		$campus = new Campus;
		$campus->description = 'Blah blah';
		$campus->save();

		$this->assertTrue($this->check_for_data('description', 'Blah blah'));
	}

	public function testAddIdentifier() 
	{
		$campus = new Campus;
		$campus->identifier = 1;
		$campus->save();

		$this->assertCount(1, Campus::all());
		$this->assertTrue($this->check_for_data('identifier', 1));
	}

	public function testIdentifierMustMeNumeric()
	{
		$this->input['identifier'] = 'AAA';
		
		Request::foundation()->request->add($this->input);

		$this->assertFalse(Campus::is_valid());
	}

	public function testAddTitle() 
	{
		$campus = new Campus;
		$campus->title = 'Some Campus';
		$campus->save();

		$this->assertTrue($this->check_for_data('title', 'Some Campus'));
	}

	public function testAddAddress_1() 
	{
		$campus = new Campus;
		$campus->address_1 = 'Address 1';
		$campus->save();

		$this->assertTrue($this->check_for_data('address_1', 'Address 1'));
	}

	public function testAddress_1IsRequired()
	{
		unset($this->input['address_1']);

		Request::foundation()->request->add($this->input);

		$this->assertFalse(Campus::is_valid());
	}

	public function testAddAddress_2() 
	{
		$campus = Campus::create(array('address_2' => 'Address 2'));
		$campus->save();

		$this->assertCount(1, Campus::all());
		$this->assertTrue($this->check_for_data('address_2', 'Address 2'));
	}

	public function testAddress_2IsRequired()
	{
		unset($this->input['address_2']);

		Request::foundation()->request->add($this->input);

		$this->assertFalse(Campus::is_valid());
	}

	public function testAddAddress_3()
	{
		$campus = Campus::create(array('address_3' => 'Address 3'));
		$campus->save();

		$this->assertCount(1, Campus::all());
		$this->assertTrue($this->check_for_data('address_3', 'Address 3'));
	}

	public function testAddTown() 
	{
		$campus = Campus::create(array('town' => 'Wolverhampton'));
		$campus->save();

		$this->assertCount(1, Campus::all());
		$this->assertTrue($this->check_for_data('town', 'Wolverhampton'));
	}

	public function testAddEmail() 
	{
		$campus = Campus::create(array('email' => 'somecampus@example.com'));
		$campus->save();

		$this->assertCount(1, Campus::all());
		$this->assertTrue($this->check_for_data('email', 'somecampus@example.com'));
	}

	public function testValidFailsOnInvalidEmail() 
	{
		$this->input['email'] = 'blah blah blah@ invalid...email';

		Request::foundation()->request->add($this->input);

		$this->assertFalse(Campus::is_valid());
	}

	public function testAddFax() 
	{
		$campus = Campus::create(array('fax' => '01010101010'));
		$campus->save();

		$this->assertCount(1, Campus::all());
		$this->assertTrue($this->check_for_data('fax', '01010101010'));
	}

	public function testAddPhone() 
	{
		$campus = Campus::create(array('phone' => '07373 8737373'));
		$campus->save();

		$this->assertCount(1, Campus::all());
		$this->assertTrue($this->check_for_data('phone', '07373 8737373'));
	}

	/**
	 * For our purposes a valid phone number contains numbers, brackers pluses, hyphens and spaces.
	 */
	public function testPhoneFailsOnInvalidPhoneNumber() 
	{
		$this->input['phone'] = 'Blah haalakak invalid.';

		Request::foundation()->request->add($this->input);

		$this->assertFalse(Campus::is_valid());
	}

	/**
	 * Feeds data into testPhonePassesOnValidPhoneNumber.
	 */
	public function phone_number_provider()
	{
		return array(
			// Numbers only.
			array('056723329999'),

			// Numbers and spaces.
			array('014555 456939393'),

			// Numbers and brackets.
			array('(01345)45238'),

			// Numbers and hyphens.
			array('556-34556'),

			// Everything all the time.
			array('+44 (01345) 4599-2999')
		);
	}

	/**
	 * @dataProvider phone_number_provider
	 */
	public function testPhonePassesOnValidPhoneNumber($phone)
	{
		$this->input['phone'] = $phone;

		Request::foundation()->request->add($this->input);

		$this->assertTrue(Campus::is_valid());
	}

	public function testAddPostcode() 
	{
		$campus = Campus::create(array('postcode' => 'CT4 2JS'));
		$campus->save();

		$this->assertCount(1, Campus::all());
		$this->assertTrue($this->check_for_data('postcode', 'CT4 2JS'));
	}

	public function testPostcodeIsRequired()
	{
		unset($this->input['postcode']);

		Request::foundation()->request->add($this->input);

		$this->assertFalse(Campus::is_valid());
	}

	public function testAddURL()
	{
		$campus = Campus::create(array('url' => 'http://example.com'));
		$campus->save();

		$this->assertCount(1, Campus::all());
		$this->assertTrue($this->check_for_data('url', 'http://example.com'));
	}

	public function testURLFailsOnOInvalidURL() 
	{
		$this->input['url'] = 'blah blah not valid url';

		Request::foundation()->request->add($this->input);

		$this->assertFalse(Campus::is_valid());
	}

}
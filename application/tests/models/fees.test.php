<?php

class TestFees extends ModelTestCase 
{

	private static $original_fee_path;

	public function __construct()
	{
		static::$original_fee_path = Config::get('fees.path');
	}

	public static function tear_down()
	{
		Cache::flush();
		parent::tear_down();
	}

	public function setUp()
	{
		Config::set('fees.path', static::$original_fee_path);
	}

	/**
	 * Set up the database.
	 */
	public static function setUpBeforeClass()
	{
		Tests\Helper::migrate();

		// Remove all elements in the awards table.
		// These are added by the Create_Intial_Awards migration.

		static::clear_models();
	}

	public function testGenerateFeeMapReturnsFalseWhenPathIsEmpty()
	{
		Config::set('fees.path', '');
		$result = Fees::generate_fee_map(2015);
		$this->assertFalse($result);
	}

	/**
	 * @expectedException PHPUnit_Framework_Error_Warning
	 */
	public function testGenerateFeeMapThrowsErrorWhenPathIsWrong()
	{
		$year = 2015;
		Config::set('fees.path', '/tmp/blah');
		$result = Fees::generate_fee_map($year);
	}

	public function testGenerateFeeMapGeneratesDataHash()
	{
		$year = 2015;
		$mapping_hash_cache = "fee-mapping-hash-{$year}";

		$hash_pre_function = Cache::get($mapping_hash_cache);
		$this->assertEmpty($hash_pre_function);

		$result = Fees::generate_fee_map($year);
		$hash_post_function = Cache::get($mapping_hash_cache);
		$this->assertNotEmpty($hash_post_function);
	}

	public function testGenerateFeeMapReturnsTrueWhenOldAndNewCacheAreTheSame()
	{
		$year = 2015;
		$mapping_hash_cache = "fee-mapping-hash-{$year}";

		$result = Fees::generate_fee_map($year);
		$hash_pre_function = Cache::get($mapping_hash_cache);
		
		$result2 = Fees::generate_fee_map($year);
		$hash_post_function = Cache::get($mapping_hash_cache);
		$this->assertTrue($result2);

		$this->assertEquals($hash_pre_function, $hash_post_function);
	}

	public function testGenerateFeeMapGeneratesNewMapWhenCacheDoesNotExist()
	{
		$year = 2015;
		$mapping_hash_cache = "fee-mapping-hash-{$year}";

		$result = Fees::generate_fee_map($year);
		$hash_pre_function = Cache::get($mapping_hash_cache);
		
		$result2 = Fees::generate_fee_map($year, false);
		$hash_post_function = Cache::get($mapping_hash_cache);
		$this->assertTrue($result2 !== true);
	}

	public function testGenerateFeeMapGeneratesNewMapWhenDataChanges()
	{
		$year = 2015;
		$mapping_hash_cache = "fee-mapping-hash-{$year}";

		$result = Fees::generate_fee_map($year);
		$hash_pre_function = Cache::get($mapping_hash_cache);

		Config::set('fees.path', static::$original_fee_path . '/testvariant');
		
		$result2 = Fees::generate_fee_map($year);
		$hash_post_function = Cache::get($mapping_hash_cache);
		$this->assertTrue($result2 !== true);

		$this->assertNotEquals($hash_pre_function, $hash_post_function);
	}

	public function testGenerateFeeMapGeneratesMapInCorrectFormat()
	{
		$year = 2015;
		$result = Fees::generate_fee_map($year);

		foreach ($result as $key => $value) {
			$this->assertNotEmpty($key);
			$this->assertNotEmpty($value);

			$this->assertArrayHasKey('home', $value);
			$this->assertArrayHasKey('int', $value);
		}

	}

	public function testLoadCSVFromWebserviceReturnsFeebandsInCorrectFormat()
	{
		$year = 2015;
		$path = Config::get('fees.path');
		$fees = Fees::load_csv_from_webservice("{$path}/{$year}-feebands.csv");
		$first_fee = $fees[0];
		$this->assertArrayHasKey('band', $first_fee);
		$this->assertArrayHasKey('full-time', $first_fee);
		$this->assertArrayHasKey('part-time', $first_fee);
		$this->assertArrayHasKey('euro-full-time', $first_fee);
		$this->assertArrayHasKey('euro-part-time', $first_fee);
	}

	public function testLoadCSVFromWebserviceReturnsMappingInCorrectFormat()
	{
		$year = 2015;
		$path = Config::get('fees.path');
		$courses = Fees::load_csv_from_webservice("{$path}/{$year}-mapping.csv");
		$first_course = $courses[0];
		$this->assertArrayHasKey('Pos Code', $first_course);
		$this->assertArrayHasKey('UK/EU Fee Band', $first_course);
		$this->assertArrayHasKey('Int Fee Band', $first_course);
	}


}
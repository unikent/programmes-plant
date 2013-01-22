<?php

class TestAPI extends ModelTestCase 
{

	public static $test_programme = array(
		'programme_title_1' => 'Thing',
		'year' => "2014",
		'programme_suspended_53' => '',
        'programme_withdrawn_54' => ''
    );

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

	public function tearDown()
	{	
		static::clear_models();

		Cache::flush();
		parent::tearDown();
	}

	// Test data
	private function publish_programme(){
		$programme = Programme::create(static::$test_programme);
        $revision = $programme->get_active_revision();
        $programme->make_revision_live($revision);
	}
	private function publish_globals(){
		$global = GlobalSetting::create(array('year' => '2014'));
        $revision = $global->get_active_revision();
        $global->make_revision_live($revision);
	}
	private function publish_programme_settings(){
		$setting = ProgrammeSetting::create(array('year' => '2014'));
        $revision = $setting->get_active_revision();
        $setting->make_revision_live($revision);
	}

	// Check index generates
	public function testget_index_returns_result_with_cache()
	{

		$this->publish_programme();
		$result = API::get_index('2014');

		$this->assertEquals(1, sizeof($result));
	}
	public function testget_index_returns_result_without_cache()
	{
		$this->publish_programme();
		// Wipe cache
		Cache::flush();
		$result = API::get_index('2014');

		$this->assertEquals(1, sizeof($result));
	}

	public function testget_index_doesnt_include_unpublished_results_without_cache()
	{
		// Add first programme
		$this->publish_programme();
		// Add second
		Programme::create(static::$test_programme);
		// Wipe cache
		Cache::flush();
		$result = API::get_index('2014');

		$this->assertEquals(1, sizeof($result));
	}
	public function testget_index_doesnt_include_unpublished_results_with_cache()
	{
		// Add first programme
		$this->publish_programme();
		// Add second
		Programme::create(static::$test_programme);

		$result = API::get_index('2014');

		$this->assertEquals(1, sizeof($result));
	}

	public function testget_index_will_find_more_than_one_result()
	{
		// Add 3
		$this->publish_programme();
		$this->publish_programme();
		$this->publish_programme();
		
		$result = API::get_index('2014');

		$this->assertEquals(3, sizeof($result));
	}

	// Test we can get programme
	public function testget_programme_without_globals(){
		$this->publish_programme_settings();
		$this->publish_programme();

		$result = API::get_programme(1, '2014');
		$this->assertEquals(false, $result);
	}
	public function testget_programme_without_programmesetting(){
		$this->publish_globals();
		$this->publish_programme();

		$result = API::get_programme(1, '2014');
		$this->assertEquals(false, $result);
	}
	public function testget_programme_with_global_and_programmesetting_works_when_cached(){
		$this->publish_globals();
		$this->publish_programme_settings();
		$this->publish_programme();

		$result = API::get_programme(1, '2014');
		$this->assertEquals('Thing', $result['programme_title']);
	}
	public function testget_programme_with_global_and_programmesetting_works_when_not_cached(){
		$this->publish_globals();
		$this->publish_programme_settings();
		$this->publish_programme();
		Cache::flush();
		$result = API::get_programme(1, '2014');
		$this->assertEquals('Thing',  $result['programme_title']);
	}


	
}
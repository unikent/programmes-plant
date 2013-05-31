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

	public static function tear_down()
	{	
		static::clear_models();

		Cache::flush();
		parent::tear_down();
	}

	// Test data
	private function publish_programme(){
		$programme = Programme::create(static::$test_programme);
        $revision = $programme->get_active_revision();
        $programme->make_revision_live($revision);

        return $programme;
	}
	private function publish_globals(){
		$global = GlobalSetting::create(array('year' => '2014'));
        $revision = $global->get_active_revision();
        $global->make_revision_live($revision);

        return $global;
	}
	private function publish_programme_settings(){
		$setting = ProgrammeSetting::create(array('year' => '2014'));
        $revision = $setting->get_active_revision();
        $setting->make_revision_live($revision);

        return $setting;
	}

	public function create_subject_area(){
		return Subject::create(array('name' => 'API Test Subject'));
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

	/**
	* @expectedException MissingDataException
	*/
	public function testget_programme_without_globals(){
		$this->publish_programme_settings();
		$this->publish_programme();

		$result = API::get_programme(1, '2014');
	}
	/**
	* @expectedException MissingDataException
	*/
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

	/**
	* @expectedException NotFoundException
	*/
	public function testget_programme_fake_programme(){
		$this->publish_globals();
		$this->publish_programme_settings();

		$result = API::get_programme(7, '2014');
	}

	
	public function testget_subjects_index_course_mapping(){
		$programme = $this->publish_programme();
		$subject_area = $this->create_subject_area();
		
		$subject_area_1_field = Programme::get_subject_area_1_field();
		$programme->$subject_area_1_field = $subject_area->id;
		$programme->save();
        $programme->make_revision_live($programme->get_active_revision());
		
		$subjects_index = API::get_subjects_index($programme->year);

		$subject_found_in_index = false;

		foreach ($subjects_index as $subject_index) {
			if ($subject_index['id'] == $subject_area->id){
				$subject_found_in_index = true;
				$title_field = Programme::get_title_field();
				$this->assertEquals($subject_index['courses'][$programme->id]['name'], $programme->$title_field);
				break;
			}
		}
		
		$this->assertTrue($subject_found_in_index); 
	}

	
	public function testget_subjects_index_course_mapping_without_cache(){
		$programme = $this->publish_programme();
		$subject_area = $this->create_subject_area();
		
		$subject_area_1_field = Programme::get_subject_area_1_field();
		$programme->$subject_area_1_field = $subject_area->id;
		$programme->save();
        $programme->make_revision_live($programme->get_active_revision());
		
		$subjects_index = API::get_subjects_index($programme->year);

		Cache::flush();

		$subject_found_in_index = false;

		foreach ($subjects_index as $subject_index) {
			if ($subject_index['id'] == $subject_area->id){
				$subject_found_in_index = true;
				$title_field = Programme::get_title_field();
				$this->assertEquals($subject_index['courses'][$programme->id]['name'], $programme->$title_field);
				break;
			}
		}
		
		$this->assertTrue($subject_found_in_index);
	}

	
	public function testcreate_get_preview(){
		$programme = $this->publish_programme();
		$this->publish_globals();
		$this->publish_programme_settings();

		$title_field = Programme::get_title_field();
		$programme->$title_field = 'A new Title';
		$programme->save();

		$revision = $programme->get_active_revision();

		$preview_hash = API::create_preview($programme->id, $revision->id);

		$this->assertFalse(empty($preview_hash));

		$preview = API::get_preview($preview_hash);

		$title_field_without_id = Revisionable::trim_id_from_field_name($title_field);

		$this->assertEquals($preview[$title_field_without_id], $programme->$title_field);

	}

	public function testcreate_get_preview_without_globals_and_settings(){
		$programme = $this->publish_programme();

		$title_field = Programme::get_title_field();
		$programme->$title_field = 'A new Title';
		$programme->save();

		$revision = $programme->get_active_revision();

		$preview_hash = API::create_preview($programme->id, $revision->id);

		$this->assertFalse($preview_hash);
	}

	/**
     * @expectedException NotFoundException
     */
	public function testget_preview_without_cache(){
		$programme = $this->publish_programme();
		$this->publish_globals();
		$this->publish_programme_settings();

		$title_field = Programme::get_title_field();
		$programme->$title_field = 'A new Title';
		$programme->save();

		$revision = $programme->get_active_revision();

		$preview_hash = API::create_preview($programme->id, $revision->id);

		Cache::flush("programme-previews.preview-{$preview_hash}");

		$preview = API::get_preview($preview_hash);

	}

	
	public function testget_preview_invalid_revision(){}


	public function testget_data_with_types(){}


	public function testcombine_programme(){}


	public function testmerge_related_courses(){}


	public function testget_module_data(){}


	public function testremove_ids_from_field_names(){}


	public function testload_external_data(){}


	public function testpurge_output_cache(){}


	public function testget_last_change_time(){}


	public function testget_last_change_time_without_cache(){}


	public function testget_last_change_date_for_headers(){}


	public function testarray_to_xml(){}


	public function testget_xcrified_programme(){}

	
}
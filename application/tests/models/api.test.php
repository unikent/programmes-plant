<?php

class TestAPI extends ModelTestCase 
{

	public static function test_programme ()
	{
		return array(
			'programme_title_1' => 'Thing',
			'year' => "2014",
			UG_Programme::get_programme_suspended_field() => '',
	        UG_Programme::get_programme_withdrawn_field() => '',
	        UG_Programme::get_subject_area_1_field()  => '1'
    	);
    }

    public static $data_types = array('campus', 'ug_award', 'faculty', 'ug_leaflet', 'school', 'ug_subject', 'ug_subjectcategory');

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
		$programme = UG_Programme::create(static::test_programme());
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
		$setting = UG_ProgrammeSetting::create(array('year' => '2014'));
        $revision = $setting->get_active_revision();
        $setting->make_revision_live($revision);

        return $setting;
	}

	public function create_data_types(){
		$data_type_objects = array();
		
		foreach (static::$data_types as $data_type) {
			$data_type_objects[$data_type] = $data_type::create(array('name' => "API Test {$data_type}"));
		}
		

		return $data_type_objects;
	}

	// Check index generates
	public function testget_index_returns_result_with_cache()
	{
		static::setURI('ug','2014');
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
		UG_Programme::create(static::test_programme());
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
		UG_Programme::create(static::test_programme());

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

		$result = API::get_programme( 'ug', '2014',1);
	}
	/**
	* @expectedException MissingDataException
	*/
	public function testget_programme_without_programmesetting(){
		$this->publish_globals();
		$this->publish_programme();

		$result = API::get_programme( 'ug', '2014',1);
		$this->assertEquals(false, $result);
	}
	public function testget_programme_with_global_and_programmesetting_works_when_cached(){
		$this->publish_globals();
		$this->publish_programme_settings();
		$this->publish_programme();

		$result = API::get_programme( 'ug', '2014',1);
		$this->assertEquals('Thing', $result['programme_title']);
	}
	public function testget_programme_with_global_and_programmesetting_works_when_not_cached(){
		$this->publish_globals();
		$this->publish_programme_settings();
		$this->publish_programme();
		Cache::flush();
		$result = API::get_programme( 'ug', '2014',1);
		$this->assertEquals('Thing',  $result['programme_title']);
	}

	/**
	* @expectedException NotFoundException
	*/
	public function testget_programme_fake_programme(){
		$this->publish_globals();
		$this->publish_programme_settings();

		$result = API::get_programme('ug', '2014', 7);
	}

	
	public function testget_subjects_index_course_mapping(){
		$programme = $this->publish_programme();
		$data_type_objects = $this->create_data_types();

		$subject_area = $data_type_objects['ug_subject'];
		
		$subject_area_1_field = UG_Programme::get_subject_area_1_field();
		$programme->$subject_area_1_field = $subject_area->id;
		$programme->save();
        $programme->make_revision_live($programme->get_active_revision());
		
		$subjects_index = API::get_subjects_index($programme->year);

		$subject_found_in_index = false;

		foreach ($subjects_index as $subject_index) {
			if ($subject_index['id'] == $subject_area->id){
				$subject_found_in_index = true;
				$title_field = UG_Programme::get_title_field();
				$this->assertEquals($subject_index['courses'][$programme->id]['name'], $programme->$title_field);
				break;
			}
		}
		
		$this->assertTrue($subject_found_in_index); 
	}

	
	public function testget_subjects_index_course_mapping_without_cache(){
		$programme = $this->publish_programme();
		$data_type_objects = $this->create_data_types();
		$subject_area = $data_type_objects['ug_subject'];
		
		$subject_area_1_field = UG_Programme::get_subject_area_1_field();
		$programme->$subject_area_1_field = $subject_area->id;
		$programme->save();
        $programme->make_revision_live($programme->get_active_revision());
		
		$subjects_index = API::get_subjects_index($programme->year);

		Cache::flush();

		$subject_found_in_index = false;

		foreach ($subjects_index as $subject_index) {
			if ($subject_index['id'] == $subject_area->id){
				$subject_found_in_index = true;
				$title_field = UG_Programme::get_title_field();
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

		$title_field = UG_Programme::get_title_field();
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

		$title_field = UG_Programme::get_title_field();
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

		$title_field = UG_Programme::get_title_field();
		$programme->$title_field = 'A new Title';
		$programme->save();

		$revision = $programme->get_active_revision();

		$preview_hash = API::create_preview($programme->id, $revision->id);

		Cache::flush("programme-previews.preview-{$preview_hash}");

		$preview = API::get_preview($preview_hash);

	}

	/**
     * @expectedException RevisioningException
     */
	public function testcreate_preview_invalid_revision(){
		$programme = $this->publish_programme();
		$this->publish_globals();
		$this->publish_programme_settings();

		$title_field = UG_Programme::get_title_field();
		$programme->$title_field = 'A new Title';
		$programme->save();

		// there is no revision with an id of 500
		$preview_hash = API::create_preview($programme->id, 500);
	}


	public function testget_data_with_types(){
		$this->create_data_types();
		foreach (static::$data_types as $type) {
			$data = API::get_data($type);
			$this->assertNotEmpty($data);
		}
	}

	/**
     * @expectedException NotFoundException
     */
	public function testget_unknown_data_type(){
		$data = API::get_data("blahBlah");
	}


	public function testcombine_programme(){
		$programme = $this->publish_programme();
		$globals = $this->publish_globals();
		$programme_settings = $this->publish_programme_settings();

		$api_programme 	= UG_Programme::get_api_programme($programme->id, $programme->year);
		$api_globals = GlobalSetting::get_api_data($globals->year);
		$api_programme_settings = UG_ProgrammeSetting::get_api_data($programme_settings->year);
		
		$combined_programme = API::combine_programme($api_programme, $api_programme_settings, $api_globals);

		$this->assertNotEmpty($combined_programme);

		$this->markTestIncomplete(
          'This needs further tests to check that various aspects of the combine worked'
        );
	}


	public function testmerge_related_courses(){

		$programme = $this->publish_programme();
		$related_programme = $this->publish_programme();
		$another_related_programme = $this->publish_programme();
		$data_type_objects = $this->create_data_types();

		$title_field = UG_Programme::get_title_field();
		$subject_area_1_field = UG_Programme::get_subject_area_1_field();

		$programme->$subject_area_1_field = $data_type_objects['ug_subject']->id;
		$programme->save();	
		$programme->make_revision_live($programme->get_active_revision());

		$related_programme->$title_field = "Related thing";
		$related_programme->$subject_area_1_field = $data_type_objects['ug_subject']->id;
		$related_programme->save();	
		$related_programme->make_revision_live($related_programme->get_active_revision());

		$another_related_programme->$title_field = "Another related thing";
		$another_related_programme->$subject_area_1_field = $data_type_objects['ug_subject']->id;
		$another_related_programme->save();	
		$another_related_programme->make_revision_live($another_related_programme->get_active_revision());

		$related_courses = UG_Programme::get_programmes_in($data_type_objects['ug_subject']->id, null, $programme->year, $programme->id);
		
		$related_courses = API::merge_related_courses($related_courses, null);

		$this->assertEquals($another_related_programme->$title_field, $related_courses[0]['name']);
		$this->assertEquals($related_programme->$title_field, $related_courses[1]['name']);
		
		$this->markTestIncomplete(
          'This test is incomplete. Should test passing a second parameter to API::merge_related_courses'
        );
	}


	public function testget_module_data(){
		$this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
	}


	public function testremove_ids_from_field_names(){
		$record = array(
			'first_record_1' => 'first_record',
			'second_record_03' => 'second_record',
			'third_record_105' => 'third_record',
			'fourth_record_99' => 'fourth_record',
			'fifth_record_999' => 'fifth_record',
			'sixth_record_' => 'sixth_record_',
			'seventh_record' => 'seventh_record'
		);

		$record_without_ids = API::remove_ids_from_field_names($record);

		foreach ($record_without_ids as $key => $value) {
			$this->assertEquals($key, $value);
		}
	}


	public function testload_external_data(){
		$this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
	}


	public function testpurge_output_cache(){
		$this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
	}


	public function testarray_to_xml(){
		$record = array(
			'column_1' => 'column_1',
			'column_2~*' => 'column_2',
			'2' => 'item'
		);

		$xml = API::array_to_xml($record);

		$xml_object = new SimpleXMLElement($xml);

		foreach ($record as $key => $value) {
			$result = $xml_object->xpath("/response/{$value}");
			while(list( , $node) = each($result)) {
				$this->assertEquals($node, $value);
				break;
			}
		}
	}

	/**
     * @expectedException MissingDataException
     */
	public function testget_xcrified_programme_no_globals_or_settings(){
		$programme = $this->publish_programme();

		$xcrified_programme = API::get_xcrified_programme($programme->id, $programme->year);
	}

	public function testget_xcrified_programme(){
		$programme = $this->publish_programme();
		$this->publish_globals();
		$this->publish_programme_settings();

		$xcrified_programme = API::get_xcrified_programme($programme->id, $programme->year);
		
		$this->assertNotEmpty($xcrified_programme);

		$this->markTestIncomplete(
          'This test has not been implemented yet. Possibly test for more specific cases'
        );
	}

	
}
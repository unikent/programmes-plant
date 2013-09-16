<?php
require_once dirname(dirname(__FILE__)) . '/lib/BaseTestCase.test.php';
require_once dirname(dirname(__FILE__)) . '/lib/ControllerTestCase.test.php';

class TestAPI_Controller extends ControllerTestCase
{
    public $input = false;

    public function populate($input)
    {
        UG_Programme::create($input)->save();
    }

    public static function setUpBeforeClass()
    {   
        Auth::login(1);
        Tests\Helper::migrate();

        // SET URIs so tests don't attempt to add _bla classes (as URI::segments() arn't populated in tests)
        static::setURI('ug','2014');
    }

    public static function tearDownAfterClass()
    {

    }

    public static function tear_down()
    {
        $programmes = UG_Programme::all();
        foreach ($programmes as $programme)
        {
            $programme->delete_for_test();
        }
        $programme_revisions = UG_ProgrammeRevision::all();
        foreach ($programme_revisions as $revision)
        {
            $revision->delete_for_test();
        }
        $global_settings = GlobalSetting::all();
        foreach ($global_settings as $setting)
        {
            $setting->delete_for_test();
        }
        $global_revisions = GlobalSettingRevision::all();
        foreach ($global_revisions as $revision)
        {
            $revision->delete_for_test();
        }
        $programme_settings = UG_ProgrammeSetting::all();
        foreach ($programme_settings as $setting)
        {
            $setting->delete_for_test();
        }
        $programme_settings_revisions = UG_ProgrammeSettingRevision::all();
        foreach ($programme_settings_revisions as $revision)
        {
            $revision->delete_for_test();
        }
        //Reset auto incriment sequences
        DB::query('delete from sqlite_sequence where name="programmes_ug"');
        DB::query('delete from sqlite_sequence where name="programmes_revisions_ug"');
        DB::query('delete from sqlite_sequence where name="global_settings"');
        DB::query('delete from sqlite_sequence where name="global_settings_revisions"');
        DB::query('delete from sqlite_sequence where name="programme_settings_ug"');
        DB::query('delete from sqlite_sequence where name="programme_settings_revisions_ug"');
        // Since we now use the normal cache, we can just flush it
        Cache::flush();

        parent::tear_down();
    }

    public function generate_programme_dependancies(){

    
        UG_ProgrammeField::create(
                array(
                        'field_name' => 'New field',
                        'field_type' => 'textarea',
                        'prefill' => 0,
                        'active' => 1,
                        'view' => 1
                    )
            )->save();

        UG_Award::create(
                array(
                        'name' => 'Hello Award',
                        'longname' => 'Long hello award',
                        'hidden' => 0
                    )
            )->save();

        Campus::create(
                array(
                        'name' => 'Hello Campus',
                        'hidden' => 0
                    )
            )->save();

        Faculty::create(
                array(
                        'name' => 'Hello Faculty',
                        'hidden' => 0
                    )
            )->save();

        UG_Leaflet::create(
                array(
                        'name' => 'Hello Leaflet',
                        'hidden' => 0
                    )
            )->save();

        School::create(
                array(
                        'name' => 'Hello School',
                        'faculties_id' => 1,
                        'hidden' => 0
                    )
            )->save();

        UG_Subject::create(
                array(
                        'name' => 'Hello Subject',
                        'hidden' => 0
                    )
            )->save();

        UG_ProgrammeSetting::create(
                array(
                        'id' => 1,
                        'year' => '2014'
                    )
            )->save();
        $ps = UG_ProgrammeSetting::find(1);
        $revision = $ps->get_revision(1);
        $ps->make_revision_live($revision);

        GlobalSetting::create(
                array(
                        'id' => 1,
                        'year' => '2014'
                    )
            )->save();
        $gs = GlobalSetting::find(1);
        $revision = $gs->get_revision(1);
        $gs->make_revision_live($revision);
    }

    public function create_programme($input = false)
    {
        if (! $input)
        {
            $input = array(
                'id' => 1, 
                UG_Programme::get_title_field() => 'Programme 1',
                'year' => '2014',
                UG_Programme::get_programme_suspended_field() => '',
                UG_Programme::get_programme_withdrawn_field() => '',
                UG_Programme::get_subject_area_1_field()  => '1'
            );
        }

        $this->populate($input);

        $course = UG_Programme::find($input['id']);
        return $course;
    }

    public function make_programme_live($id = 1)
    {
        $course = UG_Programme::find($id);
        
        if(!empty($course))
        {

            $revision = $course->get_active_revision();

            if (isset($revision)) {
                $course->make_revision_live($revision);
            }
            return $course;
        }
        
        return null;
    }

    public function testget_indexReturnsHTTPCode204WithNoDataCached()
    {
        $response = $this->get('api@index', array('2014', 'undergraduate'));
        $this->assertEquals('501', $response->status());
    }

    public function testget_indexReturnsHTTPCode200WithDataCached()
    {
        $this->generate_programme_dependancies();

        $course = $this->create_programme();
        $course = $this->make_programme_live();
    
        $response = $this->get('api@index', array($course->year, 'undergraduate'));
        $this->assertEquals('200', $response->status());
    }

    public function testget_indexReturnsJSONWithData()
    {
        $this->generate_programme_dependancies();
        
        $input = array(
            'id' => 1, 
            'programme_title_1' => 'Programme 1',
            'year' => '2014',
            UG_Programme::get_programme_suspended_field() => '',
            UG_Programme::get_programme_withdrawn_field() => '',
            );

        $course = $this->create_programme($input);

        $course = $this->make_programme_live($input['id']);

        $response = $this->get('api@index', array($input['year'], 'undergraduate'));
        $returned_data = json_decode($response->render());
        
        $returned_data = $returned_data->$input['id'];

        
        $this->assertEquals($input['id'], $returned_data->id);
        $this->assertEquals($input['programme_title_1'], $returned_data->name);
    }
    
    public function testget_indexReturnsJSONWithSuspendedWithdrawnData()
    {
        $this->generate_programme_dependancies();
        
        $input = array(
            'id' => 1, 
            'programme_title_1' => 'Programme 1',
            'year' => '2014',
            UG_Programme::get_programme_suspended_field() => '',
            UG_Programme::get_programme_withdrawn_field() => '',
        );

        $course = $this->create_programme($input);
        $course = $this->make_programme_live($input['id']);

        $response = $this->get('api@index', array($input['year'], 'undergraduate'));
        $returned_data = json_decode($response->render());
        
        $returned_data = $returned_data->$input['id'];
        
        $this->assertEquals($input['id'], $returned_data->id);
        $this->assertEquals($input['programme_title_1'], $returned_data->name);
    }

    public function testget_programmeReturns204WithNoCache()
    {


        $input = array(
            'id' => 1, 
            'programme_title_1' => 'Programme 1',
            'year' => '2014',
        );

        $course = $this->create_programme($input);
        $course = $this->make_programme_live($input['id']);

        $response = $this->get('api@programme', array($course->year, 'undergraduate', $course->id));

        $this->assertEquals('501', $response->status());
    }

    public function testget_programmeReturns200WhenCachePresent()
    {
        $this->generate_programme_dependancies();

        $input = array(
            'id' => 1, 
            'programme_title_1' => 'Programme 1',
            'year' => '2014',
            UG_Programme::get_programme_suspended_field() => '',
            UG_Programme::get_programme_withdrawn_field() => '',
            UG_Programme::get_subject_area_1_field()  => '1'
        );

        $course = $this->create_programme($input);
        $course = $this->make_programme_live($input['id']);
       
        $response = $this->get('api@programme', array($course->year, 'undergraduate', $course->id));
       
        $this->assertEquals('200', $response->status());
    }

    public function testget_programmeReturnsJSONWhenCachePresent()
    {
        $this->generate_programme_dependancies();

        $input = array(
            'id' => 1, 
            'programme_title_1' => 'Programme 1',
            'year' => '2014',
            UG_Programme::get_subject_area_1_field()  => '1'
        );
        
        $course = $this->create_programme($input);
        $course = $this->make_programme_live($input['id']);

        $response = $this->get('api@programme', array($course->year,'undergraduate', $course->id));
        $returned_data = json_decode($response->render());
        
        $this->assertEquals($input['programme_title_1'], $returned_data->programme_title);
        //perhaps some more assertions needed here
    }

    /* Check index cache */

    public function testget_index_returns_301_when_last_modified_is_after_last_changed()
    {  
        $course = $this->create_programme();
        $course = $this->make_programme_live(1);
        Cache::put('last_change', (time()-500), 10000);

        $response = $this->get('api@index', array($course->year, 'undergraduate'), array('if-modified-since'=>gmdate('D, d M Y H:i:s \G\M\T',time())));
        $this->assertEquals('304', $response->status());
    } 
    public function testget_index_returns_301_when_last_modified_is_the_same_as_last_changed()
    {   
        $course = $this->create_programme();
        $course = $this->make_programme_live(1);
        Cache::put('last_change', time(), 10000);

        $response = $this->get('api@index', array($course->year, 'undergraduate'), array('if-modified-since'=>gmdate('D, d M Y H:i:s \G\M\T',time())));
        $this->assertEquals('304', $response->status());
    }
    public function testget_index_returns_200_when_last_modified_is_before_last_changed()
    {   
        $course = $this->create_programme();
        $course = $this->make_programme_live(1);
        Cache::put('last_change', (time()+500), 10000);

        $response = $this->get('api@index', array($course->year, 'undergraduate'), array('if-modified-since'=>gmdate('D, d M Y H:i:s \G\M\T',time())));
        $this->assertEquals('200', $response->status());
    }

    /* Check subjects cache */

    public function testget_subjects_returns_301_when_last_modified_is_after_last_changed()
    {  
        $course = $this->create_programme();
        $course = $this->make_programme_live(1);
        Cache::put('last_change', (time()-500), 10000);

        $response = $this->get('api@subject_index', array($course->year, 'undergraduate'), array('if-modified-since'=>gmdate('D, d M Y H:i:s \G\M\T',time())));
        $this->assertEquals('304', $response->status());
    } 
    public function testget_subjects_returns_301_when_last_modified_is_the_same_as_last_changed()
    {   
        $course = $this->create_programme();
        $course = $this->make_programme_live(1);
        Cache::put('last_change', time(), 10000);

        $response = $this->get('api@subject_index', array($course->year, 'undergraduate'), array('if-modified-since'=>gmdate('D, d M Y H:i:s \G\M\T',time())));
        $this->assertEquals('304', $response->status());
    }
    public function testget_subjects_returns_200_when_last_modified_is_before_last_changed()
    {   
        $course = $this->create_programme();
        $course = $this->make_programme_live(1);
        Cache::put('last_change', (time()+500), 10000);

        $response = $this->get('api@subject_index', array($course->year, 'undergraduate'), array('if-modified-since'=>gmdate('D, d M Y H:i:s \G\M\T',time())));
        $this->assertEquals('200', $response->status());
    }


    /* Check programme cache */

    public function testget_programme_returns_301_when_last_modified_is_after_last_changed()
    {   
        
        $course = $this->create_programme();
        $course = $this->make_programme_live(1);

        Cache::put('last_change', (time()-500), 10000);

        $response = $this->get('api@programme', array($course->year, 'undergraduate', $course->id), array('if-modified-since'=>gmdate('D, d M Y H:i:s \G\M\T',time())));
        $this->assertEquals('304', $response->status());
    }
    public function testget_programme_returns_301_when_last_modified_is_the_same_as_last_changed()
    {   
        
        $course = $this->create_programme();
        $course = $this->make_programme_live(1);

        Cache::put('last_change', time(), 10000);

        $response = $this->get('api@programme', array($course->year, 'undergraduate', $course->id), array('if-modified-since'=>gmdate('D, d M Y H:i:s \G\M\T',time())));
        $this->assertEquals('304', $response->status());
    }
    public function testget_programme_returns_200_when_last_modified_is_before_last_changed()
    {   

        $this->generate_programme_dependancies();
        
        $course = $this->create_programme();
        $course = $this->make_programme_live(1);

        Cache::put('last_change', (time()+500), 10000);

        $response = $this->get('api@programme', array($course->year,'undergraduate', $course->id), array('if-modified-since'=>gmdate('D, d M Y H:i:s \G\M\T',time())));
        $this->assertEquals('200', $response->status());
    }

    /* Check cache itself */

    public function testget_programme_timestamp_updates_on_make_live(){
         $this->generate_programme_dependancies();
        
         $course = $this->create_programme();
         $course = $this->make_programme_live(1);

         $timestamp1 = Cache::get('last_change');

         sleep(1);
         // Modify programme
        $course->programme_title_1 = 'test 2';
        $course->save();
        $course->make_revision_live($course->get_active_revision());

        $timestamp2 = Cache::get('last_change');

        $this->assertNotEquals($timestamp1, $timestamp2 );
    }

    public function testget_programme_returns_200_when_programme_has_been_changed_since_last_cache()
    { 
            $this->generate_programme_dependancies();
        
            $course = $this->create_programme();
            $course = $this->make_programme_live(1);

            $response = $this->get('api@programme', array($course->year, 'undergraduate', $course->id));
            // Get last modified timestamp
            $timestamp = strtotime($response->foundation->headers->get("last-modified"));

            sleep(1);
            // Modify programme
            $course->programme_title_1 = 'test 2';
            $course->save();
            $course->make_revision_live($course->get_active_revision());

            // Ensure we get 200 not 301.
            $response = $this->get('api@programme', array($course->year,'undergraduate', $course->id), array('if-modified-since'=>gmdate('D, d M Y H:i:s \G\M\T',$timestamp)));
            $this->assertEquals('200', $response->status());

    }

    public function testget_xcri_capShouldReturnAnXMLContentType() {}

    public function testget_xcri_capShouldReturnA200WhenDataIsFoundForAYear() {}

    public function testget_xcri_capShouldReturnA404WhenDataIsNotFoundForAYear() {}

    public function testget_xcri_capShouldReturnDataForUndergraduatesForAYear() {}

    public function testget_xcri_capShouldReturnA404WhenGlobalSettingsThatAreRequiredAreMissing() {}

    public function testget_xcri_capShouldReturnAValidXMLDocument() {}

    public function testget_xcri_capShouldReturnAValidXCRIFeed() {}

    public function testget_xcri_capShouldReflectChangesInDataInTheXCRIFeed() {}

    public function testget_xcri_capShouldFillInAllRequiredElementsOfTheXCRIFeedOrError() {}

}
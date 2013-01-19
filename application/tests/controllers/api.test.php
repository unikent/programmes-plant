<?php
require_once dirname(dirname(__FILE__)) . '/lib/ControllerTestCase.php';

class TestAPI_Controller extends ControllerTestCase
{
    public $input = false;

    public function populate($input)
    {
        Programme::create($input)->save();
    }

    public static function setUpBeforeClass()
    {
        Tests\Helper::migrate();
    }

    public static function tearDownAfterClass()
    {

    }

    public function tearDown()
    {
        $programmes = Programme::all();
        foreach ($programmes as $programme)
        {
            $programme->delete();
        }

        $programme_revisions = ProgrammeRevision::all();
        foreach ($programme_revisions as $revision)
        {
            $revision->delete();
        }

        $global_settings = GlobalSetting::all();
        foreach ($global_settings as $setting)
        {
            $setting->delete();
        }
        $global_revisions = GlobalSettingRevision::all();
        foreach ($global_revisions as $revision)
        {
            $revision->delete();
        }

        $programme_settings = ProgrammeSetting::all();
        foreach ($programme_settings as $setting)
        {
            $setting->delete();
        }
        $programme_settings_revisions = ProgrammeSettingRevision::all();
        foreach ($programme_settings_revisions as $revision)
        {
            $revision->delete();
        }

        // Since we now use the normal cache, we can just flush it
        Cache::flush();

        parent::tearDown();
    }

    public function generate_programme_dependancies(){
        ProgrammeField::create(
                array(
                        'field_name' => 'New field',
                        'field_type' => 'textarea',
                        'prefill' => 0,
                        'active' => 1,
                        'view' => 1
                    )
            )->save();

        Award::create(
                array(
                        'name' => 'Hello Award',
                        'longname' => 'Long hello award'
                    )
            )->save();

        Campus::create(
                array(
                        'name' => 'Hello Campus'
                    )
            )->save();

        Faculty::create(
                array(
                        'name' => 'Hello Faculty'
                    )
            )->save();

        Leaflet::create(
                array(
                        'name' => 'Hello Leaflet'
                    )
            )->save();

        School::create(
                array(
                        'name' => 'Hello School',
                        'faculties_id' => 1
                    )
            )->save();

        Subject::create(
                array(
                        'name' => 'Hello Subject'
                    )
            )->save();

        ProgrammeSetting::create(
                array(
                        'id' => 1,
                        'year' => '2014'
                    )
            )->save();
        $ps = ProgrammeSetting::find(1);
        $revisions = $ps->get_revisions('selected');
        $ps->make_revision_live($revisions[0]);

        GlobalSetting::create(
                array(
                        'id' => 1,
                        'year' => '2014'
                    )
            )->save();
        $gs = GlobalSetting::find(1);
        $revisions = $gs->get_revisions('selected');
        $gs->make_revision_live($revisions[0]);
    }

    public function create_programme($input = false)
    {
        if (! $input)
        {
            $input = array(
                'id' => 1, 
                Programme::get_title_field() => 'Programme 1',
                'year' => '2014'
            );
        }

        $this->populate($input);

        $course = Programme::find($input['id']);
        return $course;
    }

    public function make_programme_live($id = 1)
    {
        $course = Programme::find($id);
        
        if(!empty($course))
        {
            $revisions = $course->get_revisions('selected');

            if (isset($revisions[0])) {
                $course->make_revision_live($revisions[0]);
            }
            return $course;
        }
        
        return null;
    }

    public function testget_indexReturnsHTTPCode204WithNoDataCached()
    {
        $response = $this->get('api@index', array('2014', 'ug'));
        $this->assertEquals('501', $response->status());
    }

    public function testget_indexReturnsHTTPCode200WithDataCached()
    {
        $this->generate_programme_dependancies();

        $course = $this->create_programme();
        $course = $this->make_programme_live($course->id);
    
        $response = $this->get('api@index', array($course->year, 'ug'));
        $this->assertEquals('200', $response->status());
    }

    public function testget_indexReturnsJSONWithData()
    {
        $this->generate_programme_dependancies();
        
        $input = array(
            'id' => 1, 
            'programme_title_1' => 'Programme 1',
            'year' => '2014',
            'programme_suspended_53' => '',
            'programme_withdrawn_54' => '',
            );

        $course = $this->create_programme($input);
        $course = $this->make_programme_live($input['id']);

        $response = $this->get('api@index', array($input['year'], 'ug'));
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
            'programme_suspended_53' => '',
            'programme_withdrawn_54' => ''
        );

        $course = $this->create_programme($input);
        $course = $this->make_programme_live($input['id']);

        $response = $this->get('api@index', array($input['year'], 'ug'));
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

        $response = $this->get('api@programme', array($course->year, 'ug', $course->id));

        $this->assertEquals('501', $response->status());
    }

    public function testget_programmeReturns200WhenCachePresent()
    {
        $this->generate_programme_dependancies();

        $input = array(
            'id' => 1, 
            'programme_title_1' => 'Programme 1',
            'year' => '2014',
        );

        $course = $this->create_programme($input);
        $course = $this->make_programme_live($input['id']);

        $response = $this->get('api@programme', array($course->year, 'ug', $course->id));

        $this->assertEquals('200', $response->status());
    }

    public function testget_programmeReturnsJSONWhenCachePresent()
    {
        $this->generate_programme_dependancies();

        $input = array(
            'id' => 1, 
            'programme_title_1' => 'Programme 1',
            'year' => '2014',
        );
        
        $course = $this->create_programme($input);
        $course = $this->make_programme_live($input['id']);

        $response = $this->get('api@programme', array($course->year, 'ug', $course->id));
        $returned_data = json_decode($response->render());
        
        $this->assertEquals($input['programme_title_1'], $returned_data->programme_title);
        //perhaps some more assertions needed here
    }
}
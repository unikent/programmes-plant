<?php

class TestProgramme extends ModelTestCase {

	public function testXcrifyReturnsAStdClass() {}

	public function testXcrifyReturnsAValidFlattenedVersionOfTheObject() {}


	public static function setUpBeforeClass()
	{
		Tests\Helper::migrate();
		static::clear_models();
	}

	public function tearDown() 
	{	
		Cache::flush(); // Flush the cache.
		static::clear_models();
		Programme::$list_cache = false;	
		parent::tearDown();
	}


	public function populate($model = 'Programme', $input = array())
	{
		if(empty($input))
		{
			$input = array('programme_title_1' => 'Thing', 'year'=> '2014' , 'created_by' => "test user");
		}

		$model::create($input);
	}

	public function populate_two_years()
	{
		$this->populate('Programme', array('year' => '2014', 'programme_title_1' => 'Thing 2014', 'id' => 1));
		$this->populate('Programme', array('year' => '2015', 'programme_title_1' => 'Thing 2015', 'id' => 2));
	}

	public function populate_cache_and_resave()
	{
		$this->populate();
		Programme::all_as_list(); // Warm cache.
		$this->resave_entry();
	}

	/**
	 * Make a minor change and save it.
	 * 
	 * Helps with all caching tests to ensure cache is wiped on save.
	 * 
	 * Always saves the programme with the ID of 1, which is, when we
	 * have two years 2014.
	 */
	public function resave_entry()
	{
		$programme = Programme::find(1);
		$programme->programme_title_1 = 'Thing 2';
		$programme->save();
	}

	public function testDeleteProgrammeDoesntDeleteProgramme()
	{
		// set up some data
    	$this->populate();
    	$programme = Programme::find(1);

    	//delete the programme
        $programme->delete();
        
        $programme = Programme::find(1);
        
        $this->assertNotNull($programme);
	}

	public function testDeleteProgrammeSetsHiddenFieldInProgramme()
	{
		// set up some data
    	$this->populate();
    	$programme = Programme::find(1);

    	//delete the programme
        $programme->delete();
        
        $programme = Programme::find(1);
        
        $this->assertEquals(true, $programme->hidden);
	}


	public function testDeleteProgrammeUnpublishesLiveRevisions()
	{
		// set up some data
    	$this->populate();
    	$programme = Programme::find(1);
    	$revision = $programme->get_revision(1);
        
        // make the revision live
        $programme->make_revision_live($revision);

    	//delete the programme
        $programme->delete();

        $revision = $programme->get_revision(1);
        
        $this->assertNotEquals('live', $revision->status);
	}

	public function testMakeRevisionLiveProgrammeSetting()
	{
		//TEST ISSUE: see "@todo @workaround" in revisionble model

    	// set up some data (set one manually as this db table is not cleared in teardown)
    	$input =  array('programme_title_1' => 'Test programme title', 'year' => '2014', 'id' => 1);
    	$this->populate('ProgrammeSetting', $input);
    	
    	$revisionable_item = ProgrammeSetting::find(1);
	
    	// make a new revision
        $new = ProgrammeSetting::find(1);
        $new->programme_title_1 = 'The Music Programme';
        $new->save();
   
        $revision = $new->get_revision(1);
        
        // make the revision live
        $new->make_revision_live($revision);
        
        // find programme #1 and check its institution name is now 'UniKent'
        $programme_setting = ProgrammeSetting::find(1);
        $this->assertEquals('The Music Programme', $programme_setting->programme_title_1); 
	}
}
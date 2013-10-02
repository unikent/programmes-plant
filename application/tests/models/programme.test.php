<?php

class TestProgramme extends ModelTestCase {

	public function testXcrifyReturnsAStdClass() {}

	public function testXcrifyReturnsAValidFlattenedVersionOfTheObject() {}


	public static function setUpBeforeClass()
	{
		Tests\Helper::migrate();
		static::clear_models();
	}

	public static function tear_down() 
	{	
		Cache::flush(); // Flush the cache.
		static::clear_models();
		UG_Programme::$list_cache = false;	
		parent::tear_down();
	}


	public function populate($model = 'UG_Programme', $input = array())
	{
		if(empty($input))
		{
			$input = array('programme_title_1' => 'Thing', 'year'=> '2014' , 'created_by' => "test user");
		}

		$model::create($input);
	}

	public function populate_two_years()
	{
		$this->populate('UG_Programme', array('year' => '2014', 'programme_title_1' => 'Thing 2014', 'id' => 1));
		$this->populate('UG_Programme', array('year' => '2015', 'programme_title_1' => 'Thing 2015', 'id' => 2));
	}

	public function populate_cache_and_resave()
	{
		$this->populate();
		UG_Programme::all_as_list(); // Warm cache.
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
		$programme = UG_Programme::find(1);
		$programme->programme_title_1 = 'Thing 2';
		$programme->save();
	}

	public function testDeleteProgrammeDoesntDeleteProgramme()
	{
		// set up some data
    	$this->populate();
    	$programme = UG_Programme::find(1);

    	//delete the programme
        $programme->delete();
        
        $programme = UG_Programme::find(1);
        
        $this->assertNotNull($programme);
	}

	public function testDeleteProgrammeSetsHiddenFieldInProgramme()
	{
		// set up some data
    	$this->populate();
    	$programme = UG_Programme::find(1);

    	//delete the programme
        $programme->delete();
        
        $programme = UG_Programme::find(1);
        
        $this->assertEquals(true, $programme->hidden);
	}


	public function testDeleteProgrammeUnpublishesLiveRevisions()
	{
		// set up some data
    	$this->populate();
    	$programme = UG_Programme::find(1);
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
    	$this->populate('UG_ProgrammeSetting', $input);
    	
    	$revisionable_item = UG_ProgrammeSetting::find(1);
	
    	// make a new revision
        $new = UG_ProgrammeSetting::find(1);
        $new->programme_title_1 = 'The Music Programme';
        $new->save();
   
        $revision = $new->get_revision(1);
        
        // make the revision live
        $new->make_revision_live($revision);
        
        // find programme #1 and check its institution name is now 'UniKent'
        $programme_setting = UG_ProgrammeSetting::find(1);
        $this->assertEquals('The Music Programme', $programme_setting->programme_title_1); 
	}

	public function testApproveRevisionSetsLiveRevisionId() {
	
    	// set up some data and a revision
		$this->populate();
    	$item = UG_Programme::find(1);
        $revision = $item->get_revision(1);
        
        // submit the revision for editing
        $item->submit_revision_for_editing($revision);
        
        // make the revision live
        $item->make_revision_live($revision);
        
        // find programme #1 and check its live_revision is the revision we've just made live
        $item = UG_Programme::find(1);
        $this->assertEquals($item->live_revision, $revision->id);
	}

	public function testget_programmes_in(){
		$this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
	}

	public function testUnpublishLiveUGRevision()
	{
		// set up some data
    	$this->populate();
    	$programme = UG_Programme::find(1);
    	$revision = $programme->get_revision(1);
        
        // make the revision live
        $programme->make_revision_live($revision);

    	//delete the programme
        $programme->unpublish_revision($revision);

        $revision = $programme->get_revision(1);
        
        $this->assertEquals('draft', $revision->status);
	}

	public function testUnpublishLivePGRevision()
	{
		// set up some data
    	$this->populate('PG_Programme');
    	$programme = PG_Programme::find(1);
    	$revision = $programme->get_revision(1);
        
        // make the revision live
        $programme->make_revision_live($revision);

    	//delete the programme
        $programme->unpublish_revision($revision);

        $revision = $programme->get_revision(1);
        
        $this->assertEquals('draft', $revision->status);
	}

}
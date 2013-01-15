<?php

class RevisionableThing extends Revisionable {}

class TestRevisionable extends ModelTestCase {

	public $input =  array('programme_title_1' => 'Thing', 'id' => 1);

	public static function setUpBeforeClass()
	{
		Tests\Helper::migrate();
	}

	public function tearDown() 
	{
		// Flush the cache.
		Cache::flush();

		// Delete from the database.
		$programme_settings = Programme::all();

		foreach ($programme_settings as $programme_setting)
		{
			$programme_setting->delete();
		}

		// Kill the list cache (just in case).
		// This caches everything in memory.
		Programme::$list_cache = false;
		
		parent::tearDown();
	}

	public function populate($model = 'Programme', $input = false)
	{

		if (! $input)
		{
			$input = $this->input;
		}
		$model::create($input);
	}

	public function testall_as_listReturnsEmptyArrayWhenWeDontHaveAnything()
	{

		
		$this->assertCount(0, Programme::all_as_list());
	}

	public function testall_as_listReturnsAnArrayOfItemsFromDatabase() 
	{
		$this->populate();

		$result = Programme::all_as_list();

		$this->assertTrue(is_array($result));
		$this->assertEquals(array('1' => 'Thing'), $result);
	}

	public function testall_as_listReturnsTheSameWhenWhenItIsInDiskCache()
	{

		$this->populate();

		// Warm up the cache.
		$before_cache = Programme::all_as_list();

		// Wipe memory cache
		Programme::$list_cache = false;

		$after_cache = Programme::all_as_list();

		$this->assertEquals($before_cache, $after_cache);
	}

	public function testall_as_listReturnsTheSameWhenWhenItIsInMemoryCache() 
	{
		// Warm up the cache.
		$before_cache = Programme::all_as_list();

		// Wipe disk cache
		Cache::forget('Programme--options-list');

		$after_cache = Programme::all_as_list();

		$this->assertEquals($before_cache, $after_cache);
	}

	public function testall_as_listResultsCacheToDiskWhenThereIsNoCache() 
	{
		$this->populate();

		Programme::all_as_list();

		$this->assertTrue(Cache::has('Programme--options-list'));
	}

	public function testall_as_listResultsCacheToMemoryWhenThereIsNoCache() {
		$this->populate();

		Programme::all_as_list();

		// Check we only have one element here.
		$this->assertCount(1, Programme::$list_cache['Programme--options-list']);
	}

	public function testall_as_listIfWeRemoveTheCacheThenWeCanStillGetList()
	{
		$this->populate();

		// Warm up cache.
		$result = Programme::all_as_list();

		// Remove the cache
		// This would be run, for example, when we save something.
		Cache::forget('Programme--options-list');

		// Check we actually forgot it.
		$this->assertFalse(Cache::has('Programme--options-list'), 'Cache has not been forgotten');

		// Everything should still work, even if we haven't cached.
		$this->assertEquals($result, Programme::all_as_list());
	}

	public function testall_as_listResultsCacheInMemoryAsWellAsOnDisk()
	{
		$this->populate();

		Programme::all_as_list();

		$this->assertEquals(array('1' => 'Thing'), Programme::$list_cache['Programme--options-list']);
	}

	public function testResultsComeFromInMemoryCacheIfItExistsNotFromDisk()
	{

		$this->populate();

		// Warm cache, presumably also the in memory cache.
		$result = Programme::all_as_list();

		// Add a false cache to the object
		$false_cache = array('1' => 'Other Thing');
		Programme::$list_cache['Programme--options-list'] = $false_cache;

		// Remove the disk cache
		Cache::forget('Programme--options-list');

		// If we get this false cache out, we know we are hitting the in memory, not the file cache.
		$this->assertEquals($false_cache, Programme::all_as_list());
	}

	public function testall_as_listResultsComeFromDiskCacheWhenCacheIsWarmedUp() 
	{
		// Artifically disk cache something.
		$false_cache = array('1' => 'Other Thing');
		Cache::forever('Programme--options-list', $false_cache);

		// We now have nothing in the database, but a cache object.
		// If we get something back then we aren't hitting the database at all.
		$this->assertEquals($false_cache, Programme::all_as_list());
	}

	public function testall_as_listResultsComeFromMemoryCacheWhenCacheIsWarmedUp()
	{
		// Artifically create a memory cache something.
		$false_cache = array('1' => 'Other Thing');
		Programme::$list_cache['Programme--options-list'] = $false_cache;

		// We have only an in memory cache, no disk and no database at all.
		// If we get something back, we are getting it from the memory cache.
		$this->assertEquals($false_cache, Programme::all_as_list());
	}

	public function testall_as_listResultsFallBackToDiskWhenMemoryIsNotPresent()
	{
		$this->populate();

		// Setup both the memory and disk cache.
		$result = Programme::all_as_list();

		// Wipe the memory cache.
		Programme::$list_cache = false;

		// Check we still have the disk cache
		$this->assertTrue(Cache::has('Programme--options-list'), 'Somehow we wiped the disk cache when we wiped the memory cache.');

		$this->assertEquals($result, Programme::all_as_list());
	}

	public function populate_two_years()
	{
		$first = array('year' => '2014', 'programme_title_1' => 'Thing 2014', 'id' => 1);
		$this->populate('Programme', $first);

		$second = array('year' => '2015', 'programme_title_1' => 'Thing 2015', 'id' => 2);
		$this->populate('Programme', $second);
	}

	public function testNall_as_listumberWeGetOutIsTheNumberWePutIn(){
		$this->populate_two_years();

		$this->assertEquals(count(Programme::all()), count(Programme::all_as_list()));
		$this->assertCount(2, Programme::all_as_list());
	}

	public function testall_as_listCheckNumberWeGetOutWithNumberWePutInWithYear()
	{
		$this->populate_two_years();

		$this->assertEquals(count(Programme::where('year', '=', '2014')), count(Programme::all_as_list(2014)));
		$this->assertCount(1, Programme::all_as_list(2014));

		$this->assertEquals(count(Programme::where('year', '=', '2015')), count(Programme::all_as_list(2015)));
		$this->assertCount(1, Programme::all_as_list(2015));
	}

	public function testall_as_listMemoryCacheSavesDifferentYears() 
	{
		$this->populate_two_years();

		// Expect only our 2015 data back.
		$this->assertEquals(array(2 => 'Thing 2015'), Programme::all_as_list(2015), "Didn't get back 2015");

		// Wipe the disk cache so we are relying on memory.
		Cache::forget('Programme-2015-options-list');

		// Expect only our 2014 data back.
		$this->assertEquals(array(1 => 'Thing 2014'), Programme::all_as_list(2014), "Didn't get back 2014");
	}

	public function testall_as_listDiskCacheSavesDifferentYears() 
	{
		$this->populate_two_years();

		// Expect only our 2015 data back.
		$this->assertEquals(array(2 => 'Thing 2015'), Programme::all_as_list(2015), "Didn't get back 2015");

		// Wipe the memory cache so we are relying on disk.
		Programme::$list_cache = false;

		// Expect only our 2014 data back.
		$this->assertEquals(array(1 => 'Thing 2014'), Programme::all_as_list(2014), "Didn't get back 2014");
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

	public function populate_cache_and_resave()
	{
		$this->populate();
		Programme::all_as_list(); // Warm cache.
		$this->resave_entry();
	}
	
	public function testsave_OnlyRemovesMemoryCacheForTheYearOnSave()
	{}

	public function testsave_RemovesMemoryCacheOnSaveWithNoYear()
	{
		$this->populate_cache_and_resave();
		$this->assertFalse(isset(Programme::$list_cache['Programme--options-list']));
	}

	public function testsave_RemovesDiscCacheOnSaveWithNoYear()
	{
		$this->populate_cache_and_resave();
		$this->assertFalse(Cache::has('Programme--options-list'));
	}

	public function testsave_RemovesMemoryCacheOnSaveWithYear()
	{
		$this->populate_two_years();

		// Warm cache
		Programme::all_as_list(2014);

		// 2014 example here I know to be ID 1
		$programme = Programme::find(1);
		$programme->programme_title_1 = 'Thing 2';
		$programme->save();

		$this->assertFalse(isset(Programme::$list_cache['Programme-2014-options-list']));
	}

	public function testsave_RemovesDiscCacheOnSaveWithYear()
	{
		$this->populate_two_years();

		// Warm cache
		Programme::all_as_list(2014);

		// 2014 example here I know to be ID 1
		$programme = Programme::find(1);
		$programme->programme_title_1 = 'Thing 2';
		$programme->save();

		$this->assertFalse(Cache::has('Programme-2014-options-list'));
	}
	
	public function testMakeRevisionLiveSetsLiveFieldToFullyPublished()
	{
    	// set up some data
    	$this->populate();
    	$revisionable_item = Programme::find(1);
        $revision = $revisionable_item->get_revision(1);
        
        // make the revision live
        $revisionable_item->make_revision_live($revision);
        
        // find programme #1 and check its 'live' value is 2
        $programme = Programme::find(1);
        $this->assertEquals(2, $programme->live);
	}
	
	public function testUseRevisionSetsLiveFieldToNothingPublishedWhenNothingPublished()
	{
    	// set up some data
    	$this->populate();
    	$programme = Programme::find(1);
        $revision = $programme->get_revision(1);
        
        // use a revision
        $programme->use_revision($revision);
        
        // find programme #1 again and now check its 'live' value is 0
        // it should be 0 because previously nothing was published and so everything should remain unpublished
        // ie we have only used a revision, not made anything live
        $programme_modified = Programme::find(1);
        $this->assertEquals(0, $programme_modified->live);
	}
	
	public function testUseRevisionSetsLiveFieldToLatestUnpublished()
	{
    	// set up some data
    	$this->populate();
    	$programme = Programme::find(1);
        $revision = $programme->get_revision(1);
        
        // make the revision live
        $programme->make_revision_live($revision);
        
        // make a new revision
        $programme_new = Programme::find(1);
        $programme_new->slug = 'test';
        $programme_new->save();
        
        // find programme #1 again and now check its 'live' value is now 1
        // it should be 1 because previously the latest version was published, but then a newer version was made
        // ie there is something newer than the live version
        $programme_modified = Programme::find(1);
        $this->assertEquals(1, $programme_modified->live);
	}
	
	public function testRevertToRevisionSetsLiveFieldToNothingPublishedWhenNothingPublished()
	{
    	// set up some data
    	$this->populate();
    	$programme = Programme::find(1);
        $revision = $programme->get_revision(1);
        
        // use a revision
        $programme->use_revision($revision);
        
        // find programme #1 again and now check its 'live' value is 0
        // it should be 0 because previously nothing was published and so everything should remain unpublished
        // ie we have only used a revision, not made anything live
        $programme_modified = Programme::find(1);
        $this->assertEquals(0, $programme_modified->live);
	}
	
	public function testRevertToRevisionSetsLiveFieldToLatestUnpublished()
	{
    	// set up some data
    	$this->populate();
    	$programme = Programme::find(1);
        $revision = $programme->get_revision(1);
        
        // use a revision
        $programme->use_revision($revision);
        
        // find programme #1 again and now check its 'live' value is 0
        // it should be 0 because previously nothing was published and so everything should remain unpublished
        // ie we have only used a revision, not made anything live
        $programme_modified = Programme::find(1);
        $this->assertEquals(0, $programme_modified->live);
	}
	
	public function testMakeRevisionLiveGlobalSetting()
	{
		 //TEST ISSUE: see "@todo @workaround" in revisionble model

    	// set up some data
    	$input =  array('institution_name_1' => 'University of Kent');
    	$this->populate('GlobalSetting', $input);
    	$revisionable_item = GlobalSetting::find(1);
    	
    	// make a new revision
        $new = GlobalSetting::find(1);
        $new->institution_name_1 = 'UniKent';
        $new->save();
        
        $revision = $new->get_revision(1);
        
        // make the revision live
        $new->make_revision_live($revision);
        
        // find programme #1 and check its institution name is now 'UniKent'
        $global_setting = GlobalSetting::find(1);
        $this->assertEquals('UniKent', $global_setting->institution_name_1);
	}
	
	public function testMakeRevisionLiveProgrammeSetting()
	{
		//TEST ISSUE: see "@todo @workaround" in revisionble model

    	// set up some data
    	$input =  array('programme_title_1' => 'Test programme title');
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

	public function testtrim_ids_from_field_namesCorrectlyRemovesIDs() {}

	public function testrrim_ids_from_field_namesReturnsStdClass() {}

}
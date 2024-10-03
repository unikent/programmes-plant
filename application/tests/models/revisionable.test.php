<?php

class RevisionableThing extends Revisionable {
    public static $revision_model = 'RevisionableThingRevision';
    protected $data_type_id = 'revisionable_thing';
}

class RevisionableThingRevision extends Revision {
    protected $data_type_id = 'revisionable_thing_id';
}

class TestRevisionable extends ModelTestCase {

	private $db_teardown = false;

	public static function setUpBeforeClass()
	{
		Tests\Helper::migrate();

		Schema::create('revisionablethings', function($table){
			$table->increments('id');
			$table->string('name', 200);
			$table->string('year', 4);
			$table->integer('instance_id');

			$table->integer('current_revision');
			$table->integer('live_revision');

			$table->timestamps();
		});

		Schema::create('revisionablethingrevisions', function($table){
			$table->increments('id');
			$table->string('name', 200);
			$table->integer('instance_id');
			$table->string('year', 4);
			$table->string('status', 200);
			$table->integer('edits_by');	
			$table->integer('made_live_by');
			$table->integer('revisionable_thing_id');
			$table->timestamps();
			$table->timestamp('published_at');
		});

		static::clear_models();
	}

	public function tearDown()
	{	
		Cache::flush(); // Flush the cache.

		if($this->db_teardown){
			DB::query('DELETE FROM revisionablethings');
			DB::query('DELETE FROM sqlite_sequence WHERE name = \'revisionablethings\'');

			DB::query('DELETE FROM revisionablethingrevisions');
			DB::query('DELETE FROM sqlite_sequence WHERE name = \'revisionablethingrevisions\'');
			$this->db_teardown = false;
		}

		RevisionableThing::$list_cache = false;	
		static::tear_down();
	}

	public function populate($model = 'RevisionableThing', $input = array())
	{
		$default = array('name' => 'Widget A', 'year' => 2014, 'id' => 1);
		$input = array_merge($default, $input);

		$model::create($input);
		$this->db_teardown = true;
	}


	public function testall_as_listReturnsEmptyArrayWhenWeDontHaveAnything()
	{
		$this->assertCount(0, RevisionableThing::all_as_list());
	}

	public function testall_as_listReturnsAnArrayOfItemsFromDatabase() 
	{
		$this->populate('RevisionableThing', array('name' => 'Widget A'));
		$result = RevisionableThing::all_as_list();

		$this->assertTrue(is_array($result));
		$this->assertEquals(array('1' => 'Widget A'), $result);
	}

	public function testall_as_listReturnsTheSameWhenItIsInDiskCache()
	{
		$this->populate();

		// Warm up the cache.
		$before_cache = RevisionableThing::all_as_list();

		// Wipe memory cache
		RevisionableThing::$list_cache = false;

		$after_cache = RevisionableThing::all_as_list();

		$this->assertEquals($before_cache, $after_cache);
	}

	public function testall_as_listReturnsTheSameWhenItIsInMemoryCache() 
	{
		$this->populate();

		// Warm up the cache.
		$before_cache = RevisionableThing::all_as_list();

		// Wipe disk cache
		Cache::forget('RevisionableThing--options-list');

		$after_cache = RevisionableThing::all_as_list();

		$this->assertEquals($before_cache, $after_cache);
	}

	public function testall_as_listResultsCacheToDiskWhenThereIsNoCache() 
	{
		$this->populate();
		RevisionableThing::all_as_list();

		$this->assertTrue(Cache::has('RevisionableThing--options-list'));
		$this->assertNotEmpty(Cache::get('RevisionableThing--options-list'));
	}

	public function testall_as_listResultsCacheToMemoryWhenThereIsNoCache()
	{
		$this->populate();
		RevisionableThing::all_as_list();

		$this->assertNotEmpty(RevisionableThing::$list_cache['RevisionableThing--options-list']);
	}

	public function testall_as_listIfWeRemoveTheCacheThenWeCanStillGetList()
	{
		$this->populate();

		// Warm up cache.
		$result = RevisionableThing::all_as_list();

		// Remove the cache
		// This would be run, for example, when we save something.
		Cache::forget('RevisionableThing--options-list');

		// Check we actually forgot it.
		$this->assertFalse(Cache::has('RevisionableThing--options-list'), 'Cache has not been forgotten');

		// Everything should still work, even if we haven't cached.
		$this->assertEquals($result, RevisionableThing::all_as_list());
	}

	public function testall_as_listResultsCacheInMemoryAsWellAsOnDisk()
	{
		$this->populate();

		RevisionableThing::all_as_list();

		$this->assertNotEmpty(RevisionableThing::$list_cache['RevisionableThing--options-list']); // Is this now the same as testall_as_listResultsCacheToMemoryWhenThereIsNoCache() ?
//		$this->assertEquals(array('1' => 'New widget!'), RevisionableThing::$list_cache['RevisionableThing--options-list']);
	}

	public function testResultsComeFromInMemoryCacheIfItExistsNotFromDisk()
	{
		$this->populate();

		// Warm cache, presumably also the in memory cache.
		$result = RevisionableThing::all_as_list();

		// Add a false cache to the object
		$false_cache = array('1' => 'Other Thing');
		RevisionableThing::$list_cache['RevisionableThing--options-list'] = $false_cache;

		// Remove the disk cache
		Cache::forget('RevisionableThing--options-list');

		// If we get this false cache out, we know we are hitting the in memory, not the file cache.
		$this->assertEquals($false_cache, RevisionableThing::all_as_list());
	}

	public function testall_as_listResultsComeFromDiskCacheWhenCacheIsWarmedUp() 
	{
		// Artifically disk cache something.
		$false_cache = array('1' => 'Other Thing');
		Cache::forever('RevisionableThing--options-list', $false_cache);

		// We now have nothing in the database, but a cache object.
		// If we get something back then we aren't hitting the database at all.
		$this->assertEquals($false_cache, RevisionableThing::all_as_list());
	}

	public function testall_as_listResultsComeFromMemoryCacheWhenCacheIsWarmedUp()
	{
		// Artifically create a memory cache something.
		$false_cache = array('1' => 'Other Thing');
		RevisionableThing::$list_cache['RevisionableThing--options-list'] = $false_cache;

		// We have only an in memory cache, no disk and no database at all.
		// If we get something back, we are getting it from the memory cache.
		$this->assertEquals($false_cache, RevisionableThing::all_as_list());
	}

	public function testall_as_listResultsFallBackToDiskWhenMemoryIsNotPresent()
	{
		$this->populate();

		// Setup both the memory and disk cache.
		$result = RevisionableThing::all_as_list();

		// Wipe the memory cache.
		RevisionableThing::$list_cache = false;

		// Check we still have the disk cache
		$this->assertTrue(Cache::has('RevisionableThing--options-list'), 'Somehow we wiped the disk cache when we wiped the memory cache.');

		$this->assertEquals($result, RevisionableThing::all_as_list());
	}


	public function testall_as_listNumberWeGetOutIsTheNumberWePutIn(){
		$this->populate('RevisionableThing', array('name' => 'Widget A', 'id' => 1));
		$this->populate('RevisionableThing', array('name' => 'Widget B', 'id' => 2));

		$this->assertCount(2, RevisionableThing::all_as_list());
		$this->assertEquals(count(RevisionableThing::all()), count(RevisionableThing::all_as_list()));
	}

	public function testall_as_listCheckNumberWeGetOutWithNumberWePutInWithYear()
	{
		$this->populate('RevisionableThing', array('name' => 'Widget 2014', 'year' => 2014, 'id' => 1));
		$this->populate('RevisionableThing', array('name' => 'Widget 2015', 'year' => 2015, 'id' => 2));

		$this->assertCount(1, RevisionableThing::all_as_list(2014));
		$this->assertEquals(count(RevisionableThing::where('year', '=', '2014')->get()), count(RevisionableThing::all_as_list(2014)));

		$this->assertEquals(count(RevisionableThing::where('year', '=', '2015')->get()), count(RevisionableThing::all_as_list(2015)));
		$this->assertCount(1, RevisionableThing::all_as_list(2015));
	}

	public function testall_as_listMemoryCacheSavesDifferentYears() 
	{
		$this->populate('RevisionableThing', array('name' => 'Widget 2014', 'year' => 2014, 'id' => 1));
		$this->populate('RevisionableThing', array('name' => 'Widget 2015', 'year' => 2015, 'id' => 2));

		// Expect only our 2015 data back.
		$this->assertEquals(array(2 => 'Widget 2015'), RevisionableThing::all_as_list(2015), "Didn't get back 2015");

		// Wipe the disk cache so we are relying on memory.
		Cache::forget('RevisionableThing-2015-options-list');

		// Expect only our 2014 data back.
		$this->assertEquals(array(1 => 'Widget 2014'), RevisionableThing::all_as_list(2014), "Didn't get back 2014");
	}

	public function testall_as_listDiskCacheSavesDifferentYears() 
	{
		$this->populate('RevisionableThing', array('name' => 'Widget 2014', 'year' => 2014, 'id' => 1));
		$this->populate('RevisionableThing', array('name' => 'Widget 2015', 'year' => 2015, 'id' => 2));

		// Expect only our 2015 data back.
		$this->assertEquals(array(2 => 'Widget 2015'), RevisionableThing::all_as_list(2015), "Didn't get back 2015");

		// Wipe the memory cache so we are relying on disk.
		RevisionableThing::$list_cache = false;

		// Expect only our 2014 data back.
		$this->assertEquals(array(1 => 'Widget 2014'), RevisionableThing::all_as_list(2014), "Didn't get back 2014");
	}

	public function testsave_RemovesMemoryCacheOnSaveWithNoYear()
	{
		$this->populate();
		RevisionableThing::all_as_list(); // Warm cache.
		$item = RevisionableThing::find(1);
		$item->name = 'Widget B';
		$item->save();

		$this->assertFalse(isset(RevisionableThing::$list_cache['RevisionableThing--options-list']));
	}

	public function testsave_RemovesDiscCacheOnSaveWithNoYear()
	{
		$this->populate();
		RevisionableThing::all_as_list(); // Warm cache.
		$item = RevisionableThing::find(1);
		$item->name = 'Widget B';
		$item->save();

		$this->assertFalse(Cache::has('RevisionableThing--options-list'));
	}


	public function testsave_RemovesMemoryCacheOnSaveWithYear()
	{
		$this->populate('RevisionableThing', array('name' => 'Widget 2014', 'year' => 2014, 'id' => 1));
		$this->populate('RevisionableThing', array('name' => 'Widget 2015', 'year' => 2015, 'id' => 2));

		// Warm cache
		RevisionableThing::all_as_list(2014);

		// 2014 example here I know to be ID 1
		$item = RevisionableThing::find(1);
		$item->name = 'Widget 2014 B';
		$item->save();

		$this->assertFalse(isset(RevisionableThing::$list_cache['RevisionableThing-2014-options-list']));
	}

	public function testsave_RemovesDiscCacheOnSaveWithYear()
	{
		$this->populate('RevisionableThing', array('name' => 'Widget 2014', 'year' => 2014, 'id' => 1));
		$this->populate('RevisionableThing', array('name' => 'Widget 2015', 'year' => 2015, 'id' => 2));

		// Warm cache
		RevisionableThing::all_as_list(2014);

		// 2014 example here I know to be ID 1
		$item = RevisionableThing::find(1);
		$item->name = 'Widget 2014 B';
		$item->save();

		$this->assertFalse(Cache::has('RevisionableThing-2014-options-list'));
	}

	
	public function testsave_OnlyRemovesMemoryCacheForTheYearOnSave()
	{}

	/**
	* @expectedException RevisioningException
	*/
	public function testget_revision_will_throw_exception_on_revision_it_doesnt_own()
	{
		$this->populate('RevisionableThing', array('name' => 'Widget A', 'id' => 1));
		$this->populate('RevisionableThing', array('name' => 'Widget B', 'id' => 2));

		$item = RevisionableThing::find(1);
		$item->get_revision(2);
		// TODO: I don't really understand how this test ever worked?
	}

	// New revision tests
	public function testRevisionCreatedOnSave()
	{
		$this->populate();

    	$item = RevisionableThing::find(1);
        $revision = $item->get_revision(1);

		$this->assertNotNull($revision);
	}

	public function testInitalRevisionForNewItemIsSelected()
	{
		$this->populate();

    	$item = RevisionableThing::find(1);
        $revision = $item->get_revision(1);

        $this->assertEquals("selected", $revision->status);
	}

	public function testSecondSaveCreatesSecondRevision()
	{	
		$this->populate();

    	$item = RevisionableThing::find(1);
    	$item->name = 'Widget B';
    	$item->save();

        $revision = $item->get_revision(2);

		$this->assertNotNull($revision);
	}

	public function testSecondSaveSetsStatusOfFirstRevisionToDraft()
	{		
		$this->populate();

    	$item = RevisionableThing::find(1);
    	$item->name = 'Widget B';
    	$item->save();

    	$revision = $item->get_revision(1);

        $this->assertEquals("draft", $revision->status);
	}

	public function testMakeRevisionLiveSetsLiveRevisionField()
	{
    	// set up some data
		$this->populate();
    	$item = RevisionableThing::find(1);
        $revision = $item->get_revision(1);

        // make the revision live
        $item->make_revision_live($revision);
        
        // find programme #1 and check its 'live' value is 2
        $item = RevisionableThing::find(1);
        $this->assertEquals($item->live_revision, $revision->id);
	}
	
	public function testUseRevisionSetsLiveFieldToNothingPublishedWhenNothingPublished()
	{
    	// set up some data
		$this->populate();

    	$revisionable_item = RevisionableThing::find(1);
        $revision = $revisionable_item->get_revision(1);
        
        // use a revision
        $revisionable_item->use_revision($revision);
        
        // find programme #1 again and now check its 'live_revision' value is 0
        // it should be 0 because previously nothing was published and so everything should remain unpublished
        // ie we have only used a revision, not made anything live
        $revisionable_item_modified = RevisionableThing::find(1);
        $this->assertEquals(0, $revisionable_item_modified->live_revision);
	}
	
	public function testUseRevisionSetsLiveFieldToLatestUnpublished()
	{
    	// set up some data
		$this->populate();

    	$item = RevisionableThing::find(1);
        $revision = $item->get_revision(1);
        
        // make the revision live
        $item->make_revision_live($revision);
        
        // make a new revision
        $new = RevisionableThing::find(1);
        $new->name = 'Widget B';
        $new->save();
        
        // find programme #1 again and now check its 'live_revision' is defferent from the current revision
        // it should be 1 because previously the latest version was published, but then a newer version was made
        // ie there is something newer than the live version
        $modified = RevisionableThing::find(1);
        $this->assertNotEquals($modified->current_revision, $modified->live_revision);
	}

/**	
	
	public function testMakeRevisionLiveGlobalSetting()
	{
		 //TEST ISSUE: see "@todo @workaround" in revisionble model

    	// set up some data (set one manually as this db table is not cleared in teardown)
    	$input =  array('institution_name_1' => 'University of Kent', 'year' => '2014', 'id' => 1);
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
**/

	public function testGetActiveRevisionAfterCreateReturnsRevision(){
		$this->populate();

		$item = RevisionableThing::find(1);
		$revision = $item->get_active_revision();

		$this->assertEquals(1, $item->id);
	}

	public function testGetActiveRevisionAfterMakeLiveReturnsRevision(){
		$this->populate();

		$item = RevisionableThing::find(1);
		$revision = $item->get_active_revision();
		$item->make_revision_live($revision);

		$this->assertEquals(1, $revision->id);
	}

	public function testget_active_revisionDoesntReturnNull() 	
	{	  	
		$this->populate();

		$item = RevisionableThing::find(1);
		$revision = $item->get_active_revision();

		$this->assertNotNull($revision);
	}

	public function testget_active_revision_for_new_item_is_selected()
	{
		$this->populate();

		$item = RevisionableThing::find(1);
		$revision = $item->get_active_revision();

		$this->assertEquals('selected', $revision->status);
	}

	public function testget_active_revision_for_new_item_is_live_once_made_live()
	{
		$this->populate();

		$item = RevisionableThing::find(1);
		$revision = $item->get_active_revision();

		$item->make_revision_live($revision);
		$revision2 = $item->get_active_revision();

		$this->assertEquals('live', $revision2->status);
	}

	public function testGetActiveRevisionAfterMakeLiveThenSaveTwoCopies(){
		$this->populate();

		$item = RevisionableThing::find(1);
		$revision = $item->get_active_revision();

		$item->make_revision_live($revision);
		$item->name = 'Widget B';
		$item->save();

		$item->name = 'Widget C';
		$item->save();

		$revision = $item->get_active_revision();

		$this->assertEquals('Widget C', $revision->name);
	}

	public function testGetLiveRevisionBeforePublishReturnsNull(){
		$this->populate();

		$item = RevisionableThing::find(1);
		$revision = $item->find_live_revision();
		//$p->make_revision_live($r);

		$this->assertEquals(null, $revision);
	}

	public function testGetLiveRevisionAfterPublishExists(){
		$this->populate();

		$item = RevisionableThing::find(1);

		$revision = $item->get_active_revision();
		$item->make_revision_live($revision);

		$revision = $item->find_live_revision();
		$this->assertEquals(1, $revision->id);
	}

	public function testtrim_id_from_field_nameCorrectlyRemovesID() {
		$to_trim_1 = 'some_field_1';
		$to_trim_2 = 'some_other_field_456';
		$to_trim_3 = 'and_another_field_5684';
		$to_trim_4 = 'field_without_id';
		$to_trim_5 = 'field_with_very_long_id_99999';

		$trimmed_1 = Revisionable::trim_id_from_field_name($to_trim_1);
		$trimmed_2 = Revisionable::trim_id_from_field_name($to_trim_2);
		$trimmed_3 = Revisionable::trim_id_from_field_name($to_trim_3);
		$trimmed_4 = Revisionable::trim_id_from_field_name($to_trim_4);
		$trimmed_5 = Revisionable::trim_id_from_field_name($to_trim_5);

		$this->assertEquals('some_field', $trimmed_1);
		$this->assertEquals('some_other_field', $trimmed_2);
		$this->assertEquals('and_another_field', $trimmed_3);
		$this->assertEquals('field_without_id', $trimmed_4);
		$this->assertEquals('field_with_very_long_id_99999', $trimmed_5);
	}

	public function testtrim_ids_from_field_namesCorrectlyRemovesIDs() {}

	public function testrrim_ids_from_field_namesReturnsStdClass() {}
	



}

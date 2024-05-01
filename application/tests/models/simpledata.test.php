<?php

class Thing extends SimpleData {
	// Things are separated by years
	public static $data_by_year = true;
}

class TestSimpleData extends ModelTestCase {

	public $input =  array('name' => 'Thing', 'id' => 1, 'hidden' => 0);

	public static function tear_down()
	{
		Thing::$rules = array();

		if (! is_null(Thing::$validation))
		{
			Thing::$validation = null;
		}

		// Flush the cache.
		Cache::flush();
		Thing::$list_cache = false;

		Schema::drop('things');

		parent::tear_down();
	}

	public function setUp()
	{
		Schema::create('things', function($table){
			$table->increments('id');
			$table->string('name');
			$table->string('year');
			$table->boolean('hidden');
			$table->timestamps();
		});

		parent::setUp();
	}

	public function testis_validRulesAreAddedSuccessfully() 
	{
		Thing::$rules = array('id' => 'required');

		Thing::is_valid(array('name' => 'required'));

		$this->assertCount(2, Thing::$rules);
	}

	public function testis_validRulesAreOverwrittenSuccessfully()
	{
		Thing::$rules = array('id' => 'required');

		Thing::is_valid(array('id' => 'max:255'));

		$this->assertEquals('max:255', Thing::$rules['id']);
	}
	
	public function testis_validReturnsTrueWhenValidationSucceeds() 
	{
		Thing::$rules = array('email' => 'required|email', 'address' => 'required|url');

		Request::foundation()->request->add(array('email' => 'alex@example.com', 'address' => 'http://example.com'));

		$this->assertTrue(Thing::is_valid());
	}

	public function testis_validReturnsFalseWhenValidationFails()
	{
		Thing::$rules = array('id' => 'required');

		Request::foundation()->request->add(array('id' => null));

		$this->assertFalse(Thing::is_valid());
	}

	public function testpopulate_from_inputSetsModelFromInput() 
	{
		$input = array('email' => 'alex@example.com', 'address' => 'http://example.com');
		Request::foundation()->request->add($input);

		$thing = new Thing;

		$thing->is_valid();
		$thing->populate_from_input();

		$this->assertEquals($input, $thing->attributes);
	}

	/**
     * @expectedException NoValidationException
     */
	public function testpopulate_from_inputThrowsExceptionThereIsNoValidation() 
	{
		$input = array('email' => 'alex@example.com', 'address' => 'http://example.com');
		Request::foundation()->request->add($input);

		$thing = new Thing;
		$thing->populate_from_input();
	}

	// Test all the all_as_list stuff

	public function populate($model = 'Thing', $input = false)
	{
		if (! $input)
		{
			$input = $this->input;
		}

		$object = $model::create($input)->save();
	}


	public function testall_as_listAlphabeticallyOrdered()
	{
		$this->populate('Thing', array('name' => 'BBB', 'id' => 1, 'hidden' => 0));
		$this->populate('Thing', array('name' => 'DDD', 'id' => 2, 'hidden' => 0));
		$this->populate('Thing', array('name' => 'AAA', 'id' => 3, 'hidden' => 0));
		$this->populate('Thing', array('name' => 'CCC', 'id' => 4, 'hidden' => 0));

		//check results are ordered as expected (and that arrays still assoc correctly to ids)
		$this->assertEquals(array('3' => 'AAA','1' => 'BBB','4' => 'CCC','2' => 'DDD'), Thing::all_as_list());
	}

	public function testall_as_listReturnsEmptyArrayWhenWeDontHaveAnything()
	{
		$this->assertCount(0, Thing::all_as_list());
	}

	public function testall_as_listReturnsAnArrayOfItemsFromDatabase() 
	{
		$this->populate();

		$result = Thing::all_as_list();

		$this->assertTrue(is_array($result));
		$this->assertEquals(array('1' => 'Thing'), $result);
	}

	public function testall_as_listReturnsTheSameWhenWhenItIsInDiskCache()
	{
		$this->populate();

		// Warm up the cache.
		$before_cache = Thing::all_as_list();

		// Wipe memory cache
		Thing::$list_cache = false;

		$after_cache = Thing::all_as_list();

		$this->assertEquals($before_cache, $after_cache);
	}

	public function testall_as_listReturnsTheSameWhenWhenItIsInMemoryCache() 
	{
		// Warm up the cache.
		$before_cache = Thing::all_as_list();

		// Wipe disk cache
		Cache::forget('Thing--options-list');

		$after_cache = Thing::all_as_list();

		$this->assertEquals($before_cache, $after_cache);
	}

	public function testall_as_listResultsCacheToDiskWhenThereIsNoCache() 
	{
		$this->populate();

		Thing::all_as_list();

		$this->assertTrue(Cache::has('Thing--options-list'));
	}

	public function testall_as_listResultsCacheToMemoryWhenThereIsNoCache() {
		$this->populate();

		Thing::all_as_list();

		// Check we only have one element here.
		$this->assertCount(1, Thing::$list_cache['Thing--options-list']);
	}

	public function testall_as_listIfWeRemoveTheCacheThenWeCanStillGetList()
	{
		$this->populate();

		// Warm up cache.
		$result = Thing::all_as_list();

		// Remove the cache
		// This would be run, for example, when we save something.
		Cache::forget('Thing--options-list');

		// Check we actually forgot it.
		$this->assertFalse(Cache::has('Thing--options-list'), 'Cache has not been forgotten');

		// Everything should still work, even if we haven't cached.
		$this->assertEquals($result, Thing::all_as_list());
	}

	public function testall_as_listResultsCacheInMemoryAsWellAsOnDisk()
	{
		$this->populate();

		Thing::all_as_list();

		$this->assertEquals(array('1' => 'Thing'), Thing::$list_cache['Thing--options-list']);
	}

	public function testResultsComeFromInMemoryCacheIfItExistsNotFromDisk()
	{
		$this->populate();

		// Warm cache, presumably also the in memory cache.
		$result = Thing::all_as_list();

		// Add a false cache to the object
		$false_cache = array('1' => 'Other Thing');
		Thing::$list_cache['Thing--options-list'] = $false_cache;

		// Remove the disk cache
		Cache::forget('Thing--options-list');

		// If we get this false cache out, we know we are hitting the in memory, not the file cache.
		$this->assertEquals($false_cache, Thing::all_as_list());
	}

	public function testall_as_listResultsComeFromDiskCacheWhenCacheIsWarmedUp() 
	{
		// Artifically disk cache something.
		$false_cache = array('1' => 'Other Thing');
		Cache::forever('Thing--options-list', $false_cache);

		// We now have nothing in the database, but a cache object.
		// If we get something back then we aren't hitting the database at all.
		$this->assertEquals($false_cache, Thing::all_as_list());
	}

	public function testall_as_listResultsComeFromMemoryCacheWhenCacheIsWarmedUp()
	{
		// Artifically create a memory cache something.
		$false_cache = array('1' => 'Other Thing');
		Thing::$list_cache['Thing--options-list'] = $false_cache;

		// We have only an in memory cache, no disk and no database at all.
		// If we get something back, we are getting it from the memory cache.
		$this->assertEquals($false_cache, Thing::all_as_list());
	}

	public function testall_as_listResultsFallBackToDiskWhenMemoryIsNotPresent()
	{
		$this->populate();

		// Setup both the memory and disk cache.
		$result = Thing::all_as_list();

		// Wipe the memory cache.
		Thing::$list_cache = false;

		// Check we still have the disk cache
		$this->assertTrue(Cache::has('Thing--options-list'), 'Somehow we wiped the disk cache when we wiped the memory cache.');

		$this->assertEquals($result, Thing::all_as_list());
	}
	
	/**
	* test that all_as_list for an example class returns the correct empty drop-down, where $empty_default_value (which would be from the db) is set to 1
	*/
	public function testall_as_listReturnsEmptyDefault()
	{
	    $empty_default_value = 1;
	    $this->populate('Thing', array('name' => 'AAA', 'id' => 1, 'hidden' => 0));
    	$options = Thing::all_as_list(false, $empty_default_value);
    	$this->assertNotNull($options[0]);
    	$this->assertEquals(__('fields.empty_default_value'), $options[0]);
	}
	
	/**
	* test that all_as_list for an example class returns the correct empty drop-down, where $empty_default_value (which would be from the db) is set to 0
	*/
	public function testall_as_listReturnsNoDefault()
	{
	    $empty_default_value = 0;
	    $this->populate('Thing', array('name' => 'AAA', 'id' => 1, 'hidden' => 0));
    	$options = Thing::all_as_list(false, $empty_default_value);
    	$this->assertArrayNotHasKey(0, $options);
	}

	public function populate_two_years()
	{
		$first = array('year' => '2014', 'name' => 'Thing 2014', 'id' => 1, 'hidden' => 0);
		$this->populate('Thing', $first);

		$second = array('year' => '2015', 'name' => 'Thing 2015', 'id' => 2, 'hidden' => 0);
		$this->populate('Thing', $second);
	}

	public function testNall_as_listumberWeGetOutIsTheNumberWePutIn()
	{
		$this->populate_two_years();

		$this->assertEquals(count(Thing::all()), count(Thing::all_as_list()));
		$this->assertCount(2, Thing::all_as_list());
	}

	public function testall_as_listCheckNumberWeGetOutWithNumberWePutInWithYear()
	{
		$this->populate_two_years();

		$this->assertEquals(count(Thing::where('year', '=', '2014')->get()), count(Thing::all_as_list(2014)));
		$this->assertCount(1, Thing::all_as_list(2014));

		$this->assertEquals(count(Thing::where('year', '=', '2015')->get()), count(Thing::all_as_list(2015)));
		$this->assertCount(1, Thing::all_as_list(2015));
	}

	public function testall_as_listMemoryCacheSavesDifferentYears() 
	{
		$this->populate_two_years();

		// Expect only our 2015 data back.
		$this->assertEquals(array(2 => 'Thing 2015'), Thing::all_as_list(2015), "Didn't get back 2015");

		// Wipe the disk cache so we are relying on memory.
		Cache::forget('Thing-2015-options-list');

		// Expect only our 2014 data back.
		$this->assertEquals(array(1 => 'Thing 2014'), Thing::all_as_list(2014), "Didn't get back 2014");
	}

	public function testall_as_listDiskCacheSavesDifferentYears() 
	{
		$this->populate_two_years();

		// Expect only our 2015 data back.
		$this->assertEquals(array(2 => 'Thing 2015'), Thing::all_as_list(2015), "Didn't get back 2015");

		// Wipe the memory cache so we are relying on disk.
		Thing::$list_cache = false;

		// Expect only our 2014 data back.
		$this->assertEquals(array(1 => 'Thing 2014'), Thing::all_as_list(2014), "Didn't get back 2014");
	}

	/**
	 * Test related to flashing of cache on save.
	 */

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
		$thing = Thing::find(1);
		$thing->name = 'Thing 2';
		$thing->save();
	}

	public function populate_cache_and_resave()
	{
		$this->populate();
		Thing::all_as_list(); // Warm cache.
		$this->resave_entry();
	}
	
	public function testsave_OnlyRemovesMemoryCacheForTheYearOnSave()
	{}

	public function testsave_RemovesMemoryCacheOnSaveWithNoYear()
	{
		$this->populate_cache_and_resave();
		$this->assertFalse(isset(Thing::$list_cache['Thing--options-list']));
	}
	
	public function testsave_RemovesMemoryCacheOnSaveWithNoYearDefaultToNone()
	{
		$this->populate_cache_and_resave();
		$this->assertFalse(isset(Thing::$list_cache['Thing--defaulttonone-options-list']));
	}

	public function testsave_RemovesDiscCacheOnSaveWithNoYear()
	{
		$this->populate_cache_and_resave();
		$this->assertFalse(Cache::has('Thing--options-list'));
	}
	
	public function testsave_RemovesDiscCacheOnSaveWithNoYearDefaultToNone()
	{
		$this->populate_cache_and_resave();
		$this->assertFalse(Cache::has('Thing--defaulttonone-options-list'));
	}

	public function testsave_RemovesMemoryCacheOnSaveWithYear()
	{
		$this->populate_two_years();

		// Warm cache
		Thing::all_as_list(2014);

		// 2014 example here I know to be ID 1
		$thing = Thing::find(1);
		$thing->name = 'Thing 2';
		$thing->save();

		$this->assertFalse(isset(Thing::$list_cache['Thing-2014-options-list']));
	}
	
	public function testsave_RemovesMemoryCacheOnSaveWithYearDefaultToNone()
	{
		$this->populate_two_years();

		// Warm cache
		Thing::all_as_list(2014);

		// 2014 example here I know to be ID 1
		$thing = Thing::find(1);
		$thing->name = 'Thing 2';
		$thing->save();
		$this->assertFalse(isset(Thing::$list_cache['Thing-2014-defaulttonone-options-list']));
	}

	public function testsave_RemovesDiscCacheOnSaveWithYear()
	{
		$this->populate_two_years();

		// Warm cache
		Thing::all_as_list(2014);

		// 2014 example here I know to be ID 1
		$thing = Thing::find(1);
		$thing->name = 'Thing 2';
		$thing->save();

		$this->assertFalse(Cache::has('Thing-2014-options-list'));
	}
	
	public function testsave_RemovesDiscCacheOnSaveWithYearDefaultToNone()
	{
		$this->populate_two_years();

		// Warm cache
		Thing::all_as_list(2014);

		// 2014 example here I know to be ID 1
		$thing = Thing::find(1);
		$thing->name = 'Thing 2';
		$thing->save();
		$this->assertFalse(Cache::has('Thing-2014-defaulttonone-options-list'));
	}
	
	public function testDeleteItemIsSetAsHiddenTrue()
	{
		$this->populate();
		$thing = Thing::find(1);
		$thing->delete();
		$thing2 = Thing::find(1);
		$this->assertEquals(true, $thing2->hidden);	
	}
	
	public function testDeleteItemIsNotDeletedFromDatabase()
	{
		$this->populate();
		$thing = Thing::find(1);
		$thing->delete();
		$thing2 = Thing::find(1);
		$this->assertNotNull($thing2);
	}
	
	public function testAllActiveReturnsOnlyActive()
	{
		$input1 =  array('name' => 'Thing1', 'id' => 1, 'hidden' => 1);
		$this->populate('Thing', $input1);
		$input2 =  array('name' => 'Thing2', 'id' => 2, 'hidden' => 0);
		$this->populate('Thing', $input2);
		$result = Thing::all_active()->get();
		$this->assertEquals(1, count($result));
	}
	
	public function testAllActiveReturnsOnlyHiddenIsFalse()
	{
		$input1 =  array('name' => 'Thing1', 'id' => 1, 'hidden' => 1);
		$this->populate('Thing', $input1);
		$input2 =  array('name' => 'Thing2', 'id' => 2, 'hidden' => 0);
		$this->populate('Thing', $input2);
		$result = Thing::all_active()->get();
		$this->assertEquals('Thing2', $result[0]->name);
	}
	

}
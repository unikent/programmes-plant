<?php

class SomethingElse extends Revisionable {}

class TestRevisionable extends ModelTestCase
{
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
		$programme_settings = ProgrammeSetting::all();

		foreach ($programme_settings as $programme_setting)
		{
			$programme_setting->delete();
		}

		// Kill the list cache (just in case).
		// This caches everything in memory.
		ProgrammeSetting::$list_cache = false;
		
		parent::tearDown();
	}

	public function populate($model = 'ProgrammeSetting', $input = false)
	{
		if (! $input)
		{
			$input = $this->input;
		}

		$object = $model::create($input);
		$object->save();
	}

	public function testall_as_listReturnsEmptyArrayWhenWeDontHaveAnything()
	{
		$this->assertCount(0, ProgrammeSetting::all_as_list());
	}

	public function testall_as_listReturnsAnArrayOfItemsFromDatabase() 
	{
		$this->populate();

		$result = ProgrammeSetting::all_as_list();

		$this->assertTrue(is_array($result));
		$this->assertEquals(array('1' => 'Thing'), $result);
	}

	public function testall_as_listReturnsTheSameWhenWhenItIsInDiskCache()
	{
		$this->populate();

		// Warm up the cache.
		$before_cache = ProgrammeSetting::all_as_list();

		// Wipe memory cache
		ProgrammeSetting::$list_cache = false;

		$after_cache = ProgrammeSetting::all_as_list();

		$this->assertEquals($before_cache, $after_cache);
	}

	public function testall_as_listReturnsTheSameWhenWhenItIsInMemoryCache() 
	{
		// Warm up the cache.
		$before_cache = ProgrammeSetting::all_as_list();

		// Wipe disk cache
		Cache::forget('ProgrammeSetting--options-list');

		$after_cache = ProgrammeSetting::all_as_list();

		$this->assertEquals($before_cache, $after_cache);
	}

	public function testall_as_listResultsCacheToDiskWhenThereIsNoCache() 
	{
		$this->populate();

		ProgrammeSetting::all_as_list();

		$this->assertTrue(Cache::has('ProgrammeSetting--options-list'));
	}

	public function testall_as_listResultsCacheToMemoryWhenThereIsNoCache() {
		$this->populate();

		ProgrammeSetting::all_as_list();

		// Check we only have one element here.
		$this->assertCount(1, ProgrammeSetting::$list_cache['ProgrammeSetting']);
	}

	public function testall_as_listIfWeRemoveTheCacheThenWeCanStillGetList()
	{
		$this->populate();

		// Warm up cache.
		$result = ProgrammeSetting::all_as_list();

		// Remove the cache
		// This would be run, for example, when we save something.
		Cache::forget('ProgrammeSetting--options-list');

		// Check we actually forgot it.
		$this->assertFalse(Cache::has('ProgrammeSetting--options-list'), 'Cache has not been forgotten');

		// Everything should still work, even if we haven't cached.
		$this->assertEquals($result, ProgrammeSetting::all_as_list());
	}

	public function testall_as_listResultsCacheInMemoryAsWellAsOnDisk()
	{
		$this->populate();

		ProgrammeSetting::all_as_list();

		$this->assertEquals(array('1' => 'Thing'), ProgrammeSetting::$list_cache['ProgrammeSetting']);
	}

	public function testResultsComeFromInMemoryCacheIfItExistsNotFromDisk()
	{
		$this->populate();

		// Warm cache, presumably also the in memory cache.
		$result = ProgrammeSetting::all_as_list();

		// Add a false cache to the object
		$false_cache = array('1' => 'Other Thing');
		ProgrammeSetting::$list_cache['ProgrammeSetting'] = $false_cache;

		// Remove the disk cache
		Cache::forget('ProgrammeSetting--options-list');

		// If we get this false cache out, we know we are hitting the in memory, not the file cache.
		$this->assertEquals($false_cache, ProgrammeSetting::all_as_list());
	}

	public function testall_as_listResultsComeFromDiskCacheWhenCacheIsWarmedUp() 
	{
		// Artifically disk cache something.
		$false_cache = array('1' => 'Other Thing');
		Cache::forever('ProgrammeSetting--options-list', $false_cache);

		// We now have nothing in the database, but a cache object.
		// If we get something back then we aren't hitting the database at all.
		$this->assertEquals($false_cache, ProgrammeSetting::all_as_list());
	}

	public function testall_as_listResultsComeFromMemoryCacheWhenCacheIsWarmedUp()
	{
		// Artifically create a memory cache something.
		$false_cache = array('1' => 'Other Thing');
		ProgrammeSetting::$list_cache['ProgrammeSetting'] = $false_cache;

		// We have only an in memory cache, no disk and no database at all.
		// If we get something back, we are getting it from the memory cache.
		$this->assertEquals($false_cache, ProgrammeSetting::all_as_list());
	}

	public function testall_as_listResultsFallBackToDiskWhenMemoryIsNotPresent()
	{
		$this->populate();

		// Setup both the memory and disk cache.
		$result = ProgrammeSetting::all_as_list();

		// Wipe the memory cache.
		ProgrammeSetting::$list_cache = false;

		// Check we still have the disk cache
		$this->assertTrue(Cache::has('ProgrammeSetting--options-list'), 'Somehow we wiped the disk cache when we wiped the memory cache.');

		$this->assertEquals($result, ProgrammeSetting::all_as_list());
	}

}

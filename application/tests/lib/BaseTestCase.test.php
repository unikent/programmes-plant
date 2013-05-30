<?php

abstract class BaseTestCase extends PHPUnit_Framework_TestCase
{
	// Set tearDown to call static version (avoid static errors)
	public function tearDown(){

		static::tear_down();
	}
	// Static tear down (can be overwritten)
	public static function tear_down(){

		static::clean_request();
	}

	
	protected static function clean_request(){}

	// Clear models of data
	protected static function clear_models($models = array("programme", "ProgrammeRevision","programmeSetting", "ProgrammeSettingRevision", "GlobalSetting", "GlobalSettingRevision")){

		foreach($models as $model){
			// Clear data
			foreach ($model::all() as $row) $row->delete_for_test();
			// Reset index
			DB::query('delete from sqlite_sequence where name= ?', array($model::$table));
		}
	}

}
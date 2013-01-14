<?php

abstract class ModelTestCase extends PHPUnit_Framework_TestCase
{
	public function tearDown()
	{
		self::clean_request();
	}

	/**
	 * Ensures we don't have problems with dirty requests.
	 */
	protected static function clean_request(){
        $request = \Laravel\Request::foundation()->request;

        $req_keys = $request->keys();
       
        foreach($req_keys as $key){
            $request->remove($key);
        }
    }
}
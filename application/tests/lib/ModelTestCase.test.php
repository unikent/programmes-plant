<?php

abstract class ModelTestCase extends BaseTestCase
{
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
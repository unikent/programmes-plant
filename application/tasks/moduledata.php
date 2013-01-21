<?php

require_once path('base') . 'vendor/autoload.php';

class ModuleData_Task {

	/**
	 * Run the seed command.
	 * 
	 * @param array  $arguments The arguments sent to the seed command.
	 */
	public function run($arguments = array())
	{
		$module_data_obj = new ProgrammesPlant\ModuleData();
		
		$session = '2014';
		
		$type = 'ug';
		
		// get the list of programmes for this session from our own API call
		$programmes = \API::get_index('2014', 'ug');	
		
		foreach ($programmes as $id => $programme)
		{
			$programme_id = $programme['id'];
			$programme_code = $programme['pos_code'];
			$campus_id = $programme['campus_id'];
			
			// set things up in test mode
			// this will eventually be the web service call so we'll comment out both lines below
			$module_data_obj->api_target = path('base') . 'vendor/unikent/programmes-plant-modules/tests/data/programme_modules.json';
			$module_data_obj->test_mode = true;
			
			$programme_modules = $module_data_obj->get_programme_modules($programme_code, $session);
			
			$module_data_obj->api_target = '';
			
			// loop through each of the modules and get its synopsis, adding it to the object for output
			foreach ($programme_modules->response->rubric->compulsory_modules->module as $index => $module)
			{
				// the synopsis
				$module->synopsis = $module_data_obj->get_module_synopsis($module->{'-code'});
				// add synopsis to the object
				$programme_modules->response->rubric->compulsory_modules->module[$index]->synopsis = $module->synopsis;
			}
			
			// store complete dataset for this programme in cache
			$cache_key = "programme-modules.$type-$session-$programme_id";
			
			Cache::put($cache_key, $programme_modules, 2628000);
			
			echo 'module data cache generated with key ' . $cache_key;
		
		}
	}
}
	
	
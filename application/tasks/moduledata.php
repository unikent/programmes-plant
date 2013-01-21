<?php

require_once path('base') . 'vendor/autoload.php';

// included this hack for now till we get this working properly with composer
require_once path('base') . 'vendor/programmes-plant-modules/src/ProgrammesPlant/moduledata.php';

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
		
		$programmes = array(1 => 'XYZ123');
		
		foreach ($programmes as $programme_id => $programme_code)
		{
			// set things up in test mode
			$module_data_obj->api_target = path('base') . 'vendor/programmes-plant-modules/tests/data/programme_modules.json';
			$module_data_obj->test_mode = true;
			
			$programme_modules = $module_data_obj->get_programme_modules($programme_code, $session);
			
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
	
	
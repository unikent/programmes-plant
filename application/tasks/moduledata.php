<?php

require_once path('base') . 'vendor/autoload.php';

class ModuleData_Task {

    /**
     * Run the moduledata command.
     * 
     * @param array  $arguments The arguments sent to the moduledata command.
     */
    public function run($arguments = array())
    {
            
        $module_data_obj = new ProgrammesPlant\ModuleData();
        
        // ug or pg
        $type = 'ug';
        if (isset($arguments[0]) && substr($arguments[0], 0, 2) == '-l')
        {
        	$type = str_replace('-l', '', $arguments[0]) != '' ? str_replace('-l', '', $arguments[0]) : 'ug';
        }
        
        // session
        $session = '2014';
        if (isset($arguments[1]) && substr($arguments[0], 0, 2) == '-s')
        {
        	$session = str_replace('-s', '', $arguments[0]) != '' ? str_replace('-s', '', $arguments[0]) : '2014';
        }
        
        // sleep before the next iteration
        $sleepytime = 5;
        if (isset($arguments[2]) && substr($arguments[2], 0, 2) == '-t')
        {
        	$sleepytime = (int) str_replace('-t', '', $arguments[0]) != '' ? str_replace('-t', '', $arguments[0]) : 5;
        }
        
        // institution is always 0122
        $institution = '0122';
        
        // base url for the programme_module web service
        $url_programme_modules = Config::get('module.programme_module_base_url');
        
        // set the base url for the module data synopsis web service
        $url_synopsis = Config::get('module.module_data_url');
        
        // get the list of programmes for this session and programme type from our own API call
        $programmes = \API::get_index($session, $type);
        
        // loop through each programme in the index and call the two web services for each
        foreach ($programmes as $id => $programme)
        {
            // build up the full url to call for the programme_module web service for this programme
            $url_programme_modules_full = $url_programme_modules . Config::get('module.pos_code_param') . '=' . $programme['pos_code'] . '&' .
                Config::get('module.instituation_param') . '=' . $institution . '&' .
                Config::get('module.campus_param') . '=' . $programme['campus_id'] . '&' .
                Config::get('module.session_param') . '=' . $session . '&' .
                'format=json';
                
            // fake the url for now
            // this makes sure we can read from local files rather than using curl
            
            // login details for the programme module web service
            $module_data_obj->login['username'] = Config::get('module.programme_module_user');
            $module_data_obj->login['password'] = Config::get('module.programme_module_pass');
            
            // get the programme_module data object
            $programme_modules = $module_data_obj->get_programme_modules($url_programme_modules_full);
            
            // reset the login details for the synopsis web service because we don't need them
            $module_data_obj->login = array();
            
            $programme_modules_new = new stdClass;
            
            // loop through each of the modules and get its synopsis, adding it to the object for output
            foreach ($programme_modules->response->rubric->cluster as $cluster)
            {
            	
            	// set the cluster type
            	$cluster_type = '';
            	if ($cluster->compulsory == 'Y')
            	{
            		$cluster_type = 'compulsory';
            	}
            	elseif ($cluster->cluster_type == 'WILD')
            	{
            		$cluster_type = 'wildcard';
            	}
            	else
            	{
	            	$cluster_type = 'optional';
            	}
            	
            	// rebuild the structure of the programmes modules object to make things easier on the frontend
            	$programme_modules_new->stages[$cluster->academic_study_stage]->name = $cluster->stage_desc;
            	$programme_modules_new->stages[$cluster->academic_study_stage]->clusters[$cluster_type][] = $cluster;
            	
            	// set the synopsis for each module
	            foreach ($cluster->modules->module as $module_index => $module)
	            {
	            	$module->synopsis = '';
	            	if (isset($module->module_code))
	            	{
			            // set the url for this web service call
		                $url_synopsis_full = $url_synopsis . $module->module_code . '.xml';
		                // get the synopsis and add it to the programme_modules object
		                $module->synopsis = str_replace("\n", '<br>', $module_data_obj->get_module_synopsis($url_synopsis_full));
	                }
	            }
            }
            
            ksort($programme_modules_new->stages);
            // clear out the api output cache completely so we can regenerate the cache now including the new module data
            Cache::purge('api-output-'.$type);
            
            // store complete dataset for this programme in our cache
            $cache_key = "programme-modules.$type-$session-" . $programme['id'];
            Cache::put($cache_key, $programme_modules_new, 2628000);
            
            echo 'module data cache generated with key ' . $cache_key;
            
            sleep($sleepytime);
        
        }
        
    }
    
    
}
    
    
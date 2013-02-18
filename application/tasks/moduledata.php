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
        // programme session is the current session for programmes
        $programme_session = '2014';
        // module session might be different from programme session if we want to get modules from last year (eg during rollover when module data might not be complete
        $module_session = '2013';
        // interval in secs between web service hits
        $sleeptime = '5';
        // limiter (mainly for testing where we don't want module data for every single programme)
        $counter = 1;
        // institution can vary
        $institution = '0122';
        
        // parse the arguments passed in from the command line into parameter values
        $parameters = $this->parse_arguments($arguments);
        
        // display help if needed
        if ( isset($parameters['help']) )
        {
            echo $parameters['help'];
            exit;
        }
        
        // module session
        $module_session = Config::get('module.module_session');
        
        // base url for the programme_module web service
        $url_programme_modules = Config::get('module.programme_module_base_url');
        
        // set the base url for the module data synopsis web service
        $url_synopsis = Config::get('module.module_data_url');
        
        // get the list of programmes for this session and programme type from our own API call
        $programmes = \API::get_index($parameters['programme_session'], $parameters['type']);
        
        // loop through each programme in the index and call the two web services for each
        $n = 0;
        foreach ($programmes as $id => $programme)
        {
            // make sure we don't get past the counter limit
            $n++;
            if ($parameters['counter'] > 0 && $n > $parameters['counter'])
            {
                break;
            }
            
            if (strstr(strtolower($programme['awarding_institute_or_body']), 'pharmacy'))
            {
                $institution = '40406';
            }
            elseif (strstr(strtolower($programme['awarding_institute_or_body']), 'christchurch'))
            {
                $institution = '0012';
            }
            
            // build up the full url to call for the programme_module web service for this programme
            $url_programme_modules_full = $url_programme_modules . Config::get('module.pos_code_param') . '=' . $programme['pos_code'] . '&' .
                Config::get('module.institution_param') . '=' . $institution . '&' .
                Config::get('module.campus_param') . '=' . $programme['campus_id'] . '&' .
                Config::get('module.session_param') . '=' . $module_session . '&' .
                'format=json';
            
            // in test mode just use the local json file
            $module_data_obj->test_mode = $parameters['test_mode'];
            if ($module_data_obj->test_mode)
            {
                $url_programme_modules_full = $_SERVER['PWD'].'/vendor/unikent/programmes-plant-modules/tests/data/programme_modules.json';
            }
            
            // build and cache the programme's module data
            $cache_key = $this->build_programme_modules($module_data_obj, $url_programme_modules_full, $url_synopsis, $programme['id'], $parameters['type'], $parameters['programme_session']);
            
            echo "output to $cache_key\n\n";
            
            // sleep before running the next iteration of the loop ie a web service throttle
            sleep($parameters['sleeptime']);
        
        }
        
    }
    
    /**
    * modules - builds a cache based on data from the sds web service and the synopsis data from the module catalogue
    *
    * @param $arguments - array consisting of: programme (object), year (string), type (string), $test_mode (bool)
    * @return void
    */
    public function modules($arguments = array())
    {
        // base url for the programme_module web service
        $url_programme_modules = Config::get('module.programme_module_base_url');
        
        // set the base url for the module data synopsis web service
        $url_synopsis = Config::get('module.module_data_url');
        
        // get the list of programmes for this session and programme type from our own API call
        $programme = $arguments[0]; // programme data
        $programme_session = $arguments[1]; // session
        
        // module session is stored as a config option so it can be changed relatively easily
        $module_session = Config::get('module.module_session');
        $type = $arguments[2]; // ug or pg
        
        // create the module data object
        $this->module_data_obj = new ProgrammesPlant\ModuleData();
        
        // test mode lets us use a dummy json file rather than a web service
        $this->module_data_obj->test_mode = $arguments[3];
        
        // pull out the field names so we can call the appropriate fields on the programme object
        $campus_id_field = Programme::get_location_field();
        $pos_code_field = Programme::get_pos_code_field();
        $institute_field = Programme::get_awarding_institute_or_body_field();
        
        // institution can vary depending on the programme
        $institution = '0122';
        if (strstr(strtolower($programme->$institute_field), 'pharmacy'))
        {
            $institution = '40406';
        }
        elseif (strstr(strtolower($programme->$institute_field), 'christchurch'))
        {
            $institution = '0012';
        }
        
        // build up the full url to call for the programme_module web service for this programme
        $url_programme_modules_full = $url_programme_modules . Config::get('module.pos_code_param') . '=' . $programme->$pos_code_field . '&' .
            Config::get('module.institution_param') . '=' . $institution . '&' .
            Config::get('module.campus_param') . '=' . $programme->$campus_id_field . '&' .
            Config::get('module.session_param') . '=' . $module_session . '&' .
            'format=json';
            
        // in test mode just use the local json file
        if ($this->module_data_obj->test_mode)
        {
            $url_programme_modules_full = dirname(dirname(__FILE__)).'/tests/data/programme_modules.json';
        }
        
        // build the programme module data and store it in a cache
        $cache_key = $this->build_programme_modules($this->module_data_obj, $url_programme_modules_full, $url_synopsis, $programme->programme_id, $type, $programme_session);
        
    }
    
    /**
    * build_programme_modules - does the grunt work and is called by the other two methods in this task
    *
    * @param obj $module_data_obj
    * @param string $url_programme_modules_full - the url for the main web service
    * @param string $url_synopsis - the url for the synopsis data
    * @param int $programme_id
    * @param string $type (ug or pg)
    * @param string $programme_session
    * @return string $cache_key
    */
    public function build_programme_modules($module_data_obj, $url_programme_modules_full, $url_synopsis, $programme_id, $type, $programme_session)
    {
        // login details for the programme module web service
        $module_data_obj->login['username'] = Config::get('module.programme_module_user');
        $module_data_obj->login['password'] = Config::get('module.programme_module_pass');
        
        // get the programme_module data object
        $programme_modules = $module_data_obj->get_programme_modules($url_programme_modules_full);
        
        // reset the login details for the synopsis web service because we don't need them
        $module_data_obj->login = array();
        
        // set up the output object
        $programme_modules_new = new stdClass;
        $programme_modules_new->stages = array();
        
        // loop through each of the modules and get its synopsis, adding it to the object for output
        if ( isset($programme_modules->response->message) )
        {
            echo $programme_modules->response->message;
        }
        else
        {
            // make sure the cluster set is an array. If there's only one item in a cluster the web service returns it as an object rather than an array (which is not really what we want)
            if ( ! is_array($programme_modules->response->rubric->cluster) )
            {
                $programme_modules->response->rubric->cluster = array($programme_modules->response->rubric->cluster);
            }
            
            // loop through each cluster and assign it to the appropriate cluster type
            foreach ($programme_modules->response->rubric->cluster as $cluster)
            {
                if (is_object($cluster) && $cluster != null)
                {
                    // set the cluster type
                    $cluster_type = '';
                    if ($cluster->cluster_type == 'WILD')
                    {
                        $cluster_type = 'wildcard';
                    }
                    elseif ($cluster->compulsory == 'Y')
                    {
                        $cluster_type = 'compulsory';
                    }
                    elseif ($cluster->compulsory == 'N')
                    {
                        $cluster_type = 'optional';
                    }
                    
                    // make sure the modules list is always an array, even if there's only one module in the cluster
                    if ( is_object($cluster->modules->module) )
                    {
                        $cluster->modules->module = array($cluster->modules->module);
                    }
                    
                    // set the synopsis for each module (but don't bother with wildcards)
                    if ($cluster_type != 'wildcard')
                    {
                        foreach ($cluster->modules->module as $module_index => $module)
                        {
                            if (is_object($module) && $module != null)
                            {
                                $module->synopsis = '';
                                if (isset($module->module_code))
                                {
                                    // set the url for this web service call
                                    $url_synopsis_full = $url_synopsis . $module->module_code . '.xml';
                                    // get the synopsis and add it to the programme_modules object
                                    $module->synopsis = str_replace("\n", '<br>', $module_data_obj->get_module_synopsis($url_synopsis_full));
                                }
                            } // end module test
                            
                        } // endforeach
                    } //endif
                    
                    // rebuild the structure of the programmes modules object to make things easier on the frontend
                    // we now store modules in stages, with each stage broken into separate clusters
                    if ( ! isset($programme_modules_new->stages[$cluster->academic_study_stage]) ) $programme_modules_new->stages[$cluster->academic_study_stage] = new stdClass;
                    $programme_modules_new->stages[$cluster->academic_study_stage]->name = $cluster->stage_desc;
                    $programme_modules_new->stages[$cluster->academic_study_stage]->clusters[$cluster_type][] = $cluster;
                    
                    
                } // end cluster test
            } // endforeach
        } // endif
        
        // sort the stages
        ksort($programme_modules_new->stages);
        
        
        // in test mode we just return the data, without caching it
        if ($module_data_obj->test_mode)
        {
            return $programme_modules_new;
        }
        
        // clear out the api output cache completely so we can regenerate the cache now including the new module data
        try
        {
            Cache::purge('api-output-'.$type);
        }
        catch(Exception $e)
        {
            echo 'No cache to purge';
        }
        
        // store complete dataset for this programme in our cache
        $cache_key = "programme-modules.$type-$programme_session-" . $programme_id;
        Cache::put($cache_key, $programme_modules_new, 2628000);
        return $cache_key;
    }
    
    /**
    * parse_arguments - parses command line options
    *
    * @param array $arguments
    * @return array $parameters
    */
    public function parse_arguments($arguments = array())
    {
        $parameters = array();
        if ( empty($arguments) ) $arguments = array('-h');
        
        foreach ($arguments as $argument)
        {
            $switch_name = substr($argument, 0, 2);
            switch($switch_name)
            {
                // level
                case '-l':
                    $parameters['type'] = str_replace('-l', '', $argument) != '' ? str_replace('-l', '', $argument) : 'ug';
                    break;
                // programme session
                case '-s':
                    $parameters['programme_session'] = str_replace('-s', '', $argument) != '' ? str_replace('-s', '', $argument) : '2014';
                    break;
                // sleep before the next iteration
                case '-t':
                    $parameters['sleeptime'] = str_replace('-t', '', $argument) != '' ? str_replace('-t', '', $argument) : 5;
                    break;
                // counter
                case '-c':
                    $parameters['counter'] = str_replace('-c', '', $argument) != '' ? str_replace('-c', '', $argument) : 1;
                    break;
                case '-x':
                    $parameters['test_mode'] = true;
                    break;
                default:
                    $parameters['help'] = "\n\n-l - ug or pg. Defaults to ug.\n-s - programme session. Defaults to 2014.\n-m - module session. Defaults to 2014\n-t - seconds per web service call. Defaults to 5 (one request every 5 seconds).\n-c - programmes to process. Defaults to 1. 0 indicates all.\n-x - test mode.\n\n";
            }
        }
        
        return $parameters;
    }
    
    
}
    
    
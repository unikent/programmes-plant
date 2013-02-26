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
        // interval in secs between web service hits
        $sleeptime = '5';
        // limiter (mainly for testing where we don't want module data for every single programme)
        $counter = 1;
        
        // parse the arguments passed in from the command line into parameter values
        $parameters = $this->parse_arguments($arguments);
        
        // use test mode if needs be
        $module_data_obj->test_mode = $parameters['test_mode'];
        
        // display help if needed
        if ( isset($parameters['help']) )
        {
            echo $parameters['help'];
            exit;
        }
        
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
            
            $url_programme_modules_full = $this->build_url_programme_modules_full($programme, $url_programme_modules, $module_data_obj->test_mode);
            
            // build and cache the programme's module data
            $cache_key = $this->build_programme_modules($module_data_obj, $url_programme_modules_full, $url_synopsis, $programme['id'], $parameters['type'], $parameters['programme_session']);
            
            echo "output to $cache_key\n\n";
            
            // sleep before running the next iteration of the loop ie a web service throttle
            sleep($parameters['sleeptime']);
        
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
        
        $type = $arguments[2]; // ug or pg
        
        // create the module data object
        $this->module_data_obj = new ProgrammesPlant\ModuleData();
        
        // test mode lets us use a dummy json file rather than a web service
        $this->module_data_obj->test_mode = $arguments[3];
        
        $url_programme_modules_full = $this->build_url_programme_modules_full($programme, $url_programme_modules, $this->module_data_obj->test_mode);
        
        // build the programme module data and store it in a cache
        $cache_key = $this->build_programme_modules($this->module_data_obj, $url_programme_modules_full, $url_synopsis, $programme->programme_id, $type, $programme_session);
        
        // clear out the api output cache completely so we can regenerate the cache now including the new module data
        try
        {
            Cache::purge('api-output-'.$type);
        }
        catch(Exception $e)
        {
            echo 'No cache to purge';
        }
        
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
                                    $module->synopsis = str_replace("\r\n", '<br>', $module_data_obj->get_module_synopsis($url_synopsis_full));
                                }
                            } // end module test
                            
                        } // endforeach
                    } //endif
                    
                    // rebuild the structure of the programmes modules object to make things easier on the frontend
                    // we now store modules in stages, with each stage broken into separate clusters
                    // convert stage 0 to 'foundation' as this prevents problems with index 0 arrays and json arrays vs objects
                    $cluster->academic_study_stage = $cluster->academic_study_stage == '0' ? 'foundation' : $cluster->academic_study_stage;
                    
                    // if a particular stage hasn't been set before, create it as a new object
                    if ( ! isset($programme_modules_new->stages[$cluster->academic_study_stage]) ) $programme_modules_new->stages[$cluster->academic_study_stage] = new stdClass;
                    
                    // set the stage name and stage cluster array
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
        if ( empty($arguments) || $arguments[0] == '-h' ) 
        {
            $parameters['help'] = $this->help_argument();
            return $parameters;
        }
        
        // set defaults for the parameters in case they're not set
        $parameters = array();
        $parameters['type'] = 'ug';
        $parameters['programme_session'] = '2014';
        $parameters['sleeptime'] = 5;
        $parameters['counter'] = 1;
        $parameters['test_mode'] = false;
        
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
    
    
    public function help_argument()
    {
        return "\n\n-l - ug or pg. Defaults to ug.\n-s - programme session. Defaults to 2014.\n-m - module session. Defaults to 2014\n-t - seconds per web service call. Defaults to 5 (one request every 5 seconds).\n-c - programmes to process. Defaults to 1. 0 indicates all.\n-x - test mode.\n\n";
    }
    
    
    public function build_url_programme_modules_full($programme, $url_programme_modules, $test_mode = false)
    {
        // if the programme is an object, store certain values as array keys to make it consistent with when the programme is just an array
        if (is_object($programme))
        {
            // pull out the field names so we can call the appropriate fields on the programme object
            $campus_id_field = Programme::get_location_field();
            $pos_code_field = Programme::get_pos_code_field();
            $institute_field = Programme::get_awarding_institute_or_body_field();
            $module_session_field = Programme::get_module_session_field();
            
            $programme_array['module_session'] = $programme->$module_session_field;
            $programme_array['pos_code'] = $programme->$pos_code_field;
            $programme_array['campus_id'] = $programme->$campus_id_field; 
            $programme_array['awarding_institute_or_body'] = $programme->$institute_field;
            
            unset($programme);
            $programme = $programme_array;
            
        }
        
        $institution = '0122';
        // TODO - commented out the section below for now because there's uncertainty about whether we need to vary the institution id in the web service call from the standard 0122
/*
        if (strstr(strtolower($programme['awarding_institute_or_body']), 'pharmacy'))
        {
            $institution = '40406';
        }
        elseif (strstr(strtolower($programme['awarding_institute_or_body']), 'christchurch'))
        {
            $institution = '0012';
        }
*/

        // module session
        // if we have a module session field use it
        if ( isset($programme['module_session']) && $programme['module_session'] != '' )
        {
            // is the module session like a year
            if ( preg_match( '/^20[0-9][0-9]$/', $programme['module_session'] ) )
            {
                $module_session = $programme['module_session'];
            }
            // if not we don't need to bother with pulling back any module data at all
            else
            {
                return '';
            }
        }
        // otherwise use the config module session field
        else
        {
            $module_session = Config::get('module.module_session');
        }
        
        // build up the full url to call for the programme_module web service for this programme
        $url_programme_modules_full = $url_programme_modules . Config::get('module.pos_code_param') . '=' . $programme['pos_code'] . '&' .
            Config::get('module.institution_param') . '=' . $institution . '&' .
            Config::get('module.campus_param') . '=' . $programme['campus_id'] . '&' .
            Config::get('module.session_param') . '=' . $module_session . '&' .
            'format=json';
        
        // in test mode just use the local json file
        if ($test_mode)
        {
            $url_programme_modules_full = dirname(dirname(__FILE__)).'/tests/data/programme_modules.json';
        }
        
        return $url_programme_modules_full;
    }
    
    
    
}
    
    
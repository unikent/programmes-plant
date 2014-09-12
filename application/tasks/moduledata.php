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
        $parameters = $this->parse_arguments($arguments);
        // display help if needed
        if ( isset($parameters['help']) )
        {
            echo $parameters['help'];
            exit;
        }

        // load UG
        $this->load_ug_modules($parameters, \API::get_index($parameters['programme_session'], 'ug'));
        // load PG
        $this->load_pg_modules($parameters, \API::get_index($parameters['programme_session'], 'pg') );
        
        // clear out the api output cache completely so we can regenerate the cache now including the new module data
         API::purge_output_cache();
    }

    // Load UG
    protected function load_ug_modules($parameters, $programmes = array()){

        $parameters['type'] = 'ug';

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

            // Kent
            $institution = '0122';
            // get campus
            $campus_id = $programme['campus_id'];

            $module_session = $this->parse_module_session($programme['module_session'], $parameters);
            if($module_session === null)continue;

            // load data
            $programme_modules_new = $this->load_module_data($programme['pos_code'], $institution, $campus_id, $module_session);

            // store complete dataset for this programme in our cache
            // in test mode we don't cache it
            $cache_key = 'programme-modules.ug-' . $parameters['programme_session'] . '-' .  base64_encode($programme['pos_code']) . '-' . $programme['id'];
            //print_r($cache_key);die();
            // Store if not in test mode
            if ( ! $parameters['test_mode'] ) Cache::put($cache_key, $programme_modules_new, 2628000);
     
            echo "output to $cache_key\n\n";

            // sleep before running the next iteration of the loop ie a web service throttle
            sleep($parameters['sleeptime']); 
        }
    }
    

    protected function load_pg_modules($parameters, $programmes = array()){

        $parameters['type'] = 'pg';

        // loop through each programme in the index and call the two web services for each
        $n = 0;
        foreach ($programmes as $id => $programme)
        {
            // make sure we don't get past the counter limit
            $n++; if ($parameters['counter'] > 0 && $n > $parameters['counter']) break;

            // Get deliveries
            $deliveries =  PG_deliveries::get_programme_deliveries($programme['id'], $parameters['programme_session']);
            if(sizeof($deliveries) === 0)continue;
            // Kent
            $institution = '0122';
            // get campus
            $campus_id = $programme['campus_id'];
            $module_session = $this->parse_module_session($programme['module_session'], $parameters);
            if($module_session === null)continue;

            // cache modules for each delivery
            foreach($deliveries as $delivery){
                $programme_modules_new = $this->load_module_data($delivery['pos_code'], $institution, $campus_id, $module_session);

                $cache_key = 'programme-modules.pg-' . $parameters['programme_session'] . '-' . base64_encode($delivery['pos_code']) . '-' . $programme['id'];
                if ( ! $parameters['test_mode'] ) Cache::put($cache_key, $programme_modules_new, 2628000);
                sleep($parameters['sleeptime']); 
            }
            
        }
    }

    private function parse_module_session($module_session, $parameters){
         if ( $module_session != '' ){
            // is the module session like a year
            if ( preg_match( '/^20[0-9][0-9]$/', $module_session ) ){
               return $module_session ;
            } // if not we don't need to bother with pulling back any module data at all
            else {
                return null;
            }
        }  // otherwise use the config module session field  
        else {
            if ($parameters['type'] == 'ug') {
                $module_session_field = UG_Programme::get_module_session_field();
                return UG_ProgrammeSetting::get_setting($parameters['programme_session'], $module_session_field);
            }
            elseif ($parameters['type'] == 'pg') {
                $module_session_field = PG_Programme::get_module_session_field();
                return PG_ProgrammeSetting::get_setting($parameters['programme_session'], $module_session_field);
            }
        }
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
    

    //$institution = '0122';
    public function load_module_data($pos_code, $institution, $campus_id, $module_session, $module = false){

        if(!$module) $module = new ProgrammesPlant\ModuleData();

        // build request
        $webservice_request = $this->build_module_webservice_url($pos_code, $institution, $campus_id, $module_session); 

        // auth 
        $module->login['username'] = Config::get('module.programme_module_user');
        $module->login['password'] = Config::get('module.programme_module_pass');

        // load data & 
        $data = $module->get_programme_modules($webservice_request);

        // clear auth
        $module->login = array();

        //parse modules
        $data = $this->parse_module_data($data, $module);

        return $data;
    }
    public static function build_module_webservice_url( $pos_code, $institution, $campus_id, $module_session){

        if($module_session == 'None' || $module_session == 'none') return '';

        return Config::get('module.programme_module_base_url') . 
            Config::get('module.pos_code_param') . '=' . $pos_code . '&' .
            Config::get('module.institution_param') . '=' . $institution . '&' .
            Config::get('module.campus_param') . '=' . $campus_id . '&' .
            Config::get('module.session_param') . '=' . $module_session . '&' .
            'format=json';
    }

    public function parse_module_data($programme_modules, $module_data_obj)
    {
        // set up the output object
        $programme_modules_new = new stdClass;
        $programme_modules_new->stages = array();

        // return blank if no valid modules found
        if($programme_modules === null) return $programme_modules_new;
        
        // loop through each of the modules and get its synopsis, adding it to the object for output
        if ( isset($programme_modules->response->message) )
        {
            echo $programme_modules->response->message;
        }
        else
        {
            if(isset($programme_modules->response->rubric)){
                // make sure the cluster set is an array. If there's only one item in a cluster the web service returns it as an object rather than an array (which is not really what we want)
                if ( isset($programme_modules->response->rubric->cluster) && ! is_array($programme_modules->response->rubric->cluster) )
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
                                        $url_synopsis_full = Config::get('module.module_data_url') . $module->module_code . '.xml';
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
        } // endif
        
        // sort the stages
        ksort($programme_modules_new->stages);
        
        return $programme_modules_new;    
    }



    /**
    * modules - builds a cache based on data from the sds web service and the synopsis data from the module catalogue
    *
    * @param $arguments - array consisting of: programme (object), year (string), type (string), $test_mode (bool)
    * @return void
    */
    public function modules($arguments = array())
    {
        
        // get the list of programmes for this session and programme type from our own API call
        $programme = $arguments[0]; // programme data
        $programme_session = $arguments[1]; // session
        
        $type = $arguments[2]; // ug or pg

        // set params
        $parameters = array();
        $parameters['type'] = $type;
        $parameters['programme_session'] = $programme_session;
        $parameters['sleeptime'] = 1;
        $parameters['counter'] = 1;
        $parameters['test_mode'] = false;

        // set programme vars
        $model = $type.'_Programme';

        $campus_id_field = $model::get_location_field();
        $pos_code_field = $model::get_pos_code_field();
        $module_session_field = $model::get_module_session_field();

        $tmp_programme['campus_id'] = Campus::find($programme->$campus_id_field)->identifier;
        $tmp_programme['id'] = $programme->instance_id;
        $tmp_programme['module_session'] = $programme->$module_session_field;
        $tmp_programme['pos_code'] = $programme->$pos_code_field;

        $method = 'load_'.$type.'_modules';

        $this->$method($parameters, array($tmp_programme), false);
        // clear output cache
        Cache::purge('api-output-'.$type);
    }
    
    
    
}
    
    
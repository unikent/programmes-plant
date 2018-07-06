<?php

require_once path('base') . 'vendor/autoload.php';

class ModuleData_Task {

	public static $moduleCache = array();

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

        Cache::purge('api-index-ug');
        // load UG
        $parameters['type']='ug';
        $this->load_modules($parameters, \API::get_index($parameters['programme_session'], 'ug'));

        Cache::purge('api-index-pg');
        // load PG
        $parameters['type']='pg';
        $this->load_modules($parameters, \API::get_index($parameters['programme_session'], 'pg') );

        // clear out the api output cache completely so we can regenerate the cache now including the new module data
         API::purge_output_cache();
    }

    protected function load_modules($parameters, $programmes = array()){

        // loop through each programme in the index and call the two web services for each
        $n = 0;
        foreach ($programmes as $id => $programme)
        {
            // make sure we don't get past the counter limit
            $n++; if ($parameters['counter'] > 0 && $n > $parameters['counter']) break;

            echo "Programme: " . $parameters['type'] . '-' . $programme['id'] . "\n";

            $deliveryClass=  strtoupper($parameters['type']) . '_Delivery';
            // Get deliveries
            $deliveries =  $deliveryClass::get_programme_deliveries($programme['id'], $parameters['programme_session']);
            if(sizeof($deliveries) === 0)continue;
            // Kent
            $institution = '0122';
            // get campus
            $campus_id = $programme['campus_id'];
            $module_session = $this->parse_module_session($programme['module_session'], $parameters);
            echo "Module session: {$module_session}\n";
            if(empty($module_session))continue;

            $module_cache =array();
            // cache modules for each delivery
            foreach($deliveries as $delivery){

                $cache_key = 'programme-modules.' . $parameters['type'] . '-' . $parameters['programme_session'] . '-' . base64_encode($delivery['pos_code']) . '-' . $programme['id'];

                if(array_key_exists($cache_key,$module_cache)){
                    continue;
                }else {
                    $programme_modules_new = $this->load_module_data($delivery['crs_code'], $institution, $campus_id, $module_session);
                    if($programme_modules_new!==null) {
                        $module_cache[$cache_key] = $programme_modules_new;
                    }
                }

                if($programme_modules_new) {
					echo "\nStages count:" . count($programme_modules_new->stages) . "\n\n";
				}
				else {
                	echo"\nStages count: 0 - Could not load_module_data\n\n";
				}
                if ( ! $parameters['test_mode'] && $programme_modules_new!==null && $programme_modules_new!==false) Cache::put($cache_key, $programme_modules_new, 2628000);
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
        $parameters['programme_session'] = '2014';
        $parameters['sleeptime'] = 5;
        $parameters['counter'] = 1;
        $parameters['test_mode'] = false;

        foreach ($arguments as $argument)
        {
            $switch_name = substr($argument, 0, 2);
            switch($switch_name)
            {
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
                    $parameters['help'] = $this->help_argument();
            }
        }

        return $parameters;
    }


    public function help_argument()
    {
        return "\n\n-s - programme session. Defaults to 2014.\n-t - seconds per web service call. Defaults to 5 (one request every 5 seconds).\n-c - programmes to process. Defaults to 1. 0 indicates all.\n-x - test mode.\n\n";
    }


    //$institution = '0122';
    public function load_module_data($crs_code, $institution, $campus_id, $module_session, $module = false){

        if(!$module) $module = new ProgrammesPlant\ModuleData();

        if(empty($module_session)){ return false; }

        // build request
        $webservice_request = $this->build_module_webservice_url($crs_code, $institution, $campus_id, $module_session);

        // load data &
        echo "Requesting: " . $webservice_request . ' - ';
        $data = $module->get_programme_modules($webservice_request);

        // clear auth
        $module->login = array();

        //parse modules
        $data = $this->parse_module_data($data);

        return $data;
    }
    public static function build_module_webservice_url( $crs_code, $institution, $campus_id, $module_session){

        if($module_session == 'None' || $module_session == 'none') return '';

        return str_replace(array(
        	'{crs_code}',
			'{session}',
			'{institution}',
			'{campus}',
		),array(
    		$crs_code,
			$module_session,
			$institution,
			$campus_id
			),
			Config::get('module.programme_module_base_url')
		);
    }

    public function parse_module_data($programme_modules)
    {
        // set up the output object
        $programme_modules_new = new stdClass;
        $programme_modules_new->stages = array();

        // return blank if no valid modules found
        if($programme_modules === null) return null;

        // loop through each of the modules and get its synopsis, adding it to the object for output
        if ( isset($programme_modules->response->message) )
        {
            echo $programme_modules->response->message;
            return false;
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
						$newCluster = clone $cluster;
						$newCluster->modules = new stdClass();
						$newCluster->modules->module= array();

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

						foreach ($cluster->modules->module as $module_index => $module)
						{
							if (is_object($module) && $module != null && $module->module_status == "ACTIVE")
							{
								$module->synopsis = '';
								if (isset($module->module_code))
								{

									$apiData = self::getModuleAPIData($module->module_code);
									// getModuleAPIData may return "module not found"
									if(is_object($apiData)){
										$module->synopsis = str_replace("\r\n", '<br>', $apiData->synopsis);
										$module->module_code = $apiData->code;
										$module->sds_code = $apiData->sds_code;
									}
								}
								$newCluster->modules->module[] = $module;

							} // end module test

						} // endforeach


                        // rebuild the structure of the programmes modules object to make things easier on the frontend
                        // we now store modules in stages, with each stage broken into separate clusters
                        // convert stage 0 to 'foundation' as this prevents problems with index 0 arrays and json arrays vs objects
						$newCluster->academic_study_stage = $cluster->academic_study_stage == '0' ? 'foundation' : $cluster->academic_study_stage;

                        // if a particular stage hasn't been set before, create it as a new object
                        if ( ! isset($programme_modules_new->stages[$newCluster->academic_study_stage]) ) $programme_modules_new->stages[$newCluster->academic_study_stage] = new stdClass;

                        // set the stage name and stage cluster array
                        $programme_modules_new->stages[$newCluster->academic_study_stage]->name = $newCluster->stage_desc;
                        $programme_modules_new->stages[$newCluster->academic_study_stage]->clusters[$cluster_type][] = $newCluster;


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
        $parameters['sleeptime'] = 3;
        $parameters['counter'] = 0;
        $parameters['test_mode'] = false;

        // set programme vars
        $tmp_programme['campus_id'] = $programme['location'][0]['identifier'];
        $tmp_programme['id'] = $programme['instance_id'];
        $tmp_programme['module_session'] = $module_session = $this->parse_module_session($programme['module_session'], $parameters);

        $this->load_modules($parameters, array($tmp_programme['id']=>$tmp_programme));
        $cache_key = "api-output-{$type}.programme-$module_session-" . $tmp_programme['id'];
        Cache::forget($cache_key);
    }

    public static function getModuleAPIData($code){

		if(array_key_exists($code,self::$moduleCache)){
			return self::$moduleCache[$code];
		}

		$ch = curl_init( Config::get('application.api_base') . "/v1/modules/module/" . $code);

		curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
		//curl_setopt($ch, CURLOPT_PROXY, 'advocate.kent.ac.uk:3128');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		$result = curl_exec($ch);
		$retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if($result) {
			$result = json_decode($result);

			self::$moduleCache[$code] = $result;

			return $result;
		}else{
			return false;
		}

	}

}
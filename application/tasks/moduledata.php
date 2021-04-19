<?php

require_once path('base') . 'vendor/autoload.php';

class ModuleData_Task
{

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
		if (isset($parameters['help'])) {
			echo $parameters['help'];
			exit;
		}

		// if no id is specified, just do everything
		if (empty($parameters['id'])) {
			Cache::purge('api-index-ug');
			// load UG
			$parameters['type'] = 'ug';
			$this->load_modules($parameters, \API::get_index($parameters['programme_session'], 'ug'));

			Cache::purge('api-index-pg');
			// load PG
			$parameters['type'] = 'pg';
			$this->load_modules($parameters, \API::get_index($parameters['programme_session'], 'pg'));
		} else {
			// @todo does specifying a single id actually work? - what is the prupose of the module() method
			if ($parameters['type']) {
				Cache::purge('api-index-' . $parameters['type']);
				$this->load_modules($parameters, \API::get_index($parameters['programme_session'], $parameters['type']));
			} else {
				echo "ERROR - cannot specify a programme id without also specifying type (-p or -u)\n";
				echo $parameters['help'];
			}
		}
		// clear out the api output cache completely so we can regenerate the cache now including the new module data
		API::purge_output_cache();
	}

	protected function load_modules($parameters, $programmes = array())
	{

		// loop through each programme in the index and call the two web services for each
		$n = 0;
		foreach ($programmes as $id => $programme) {
			// make sure we don't get past the counter limit
			$n++;
			if ($parameters['counter'] > 0 && $n > $parameters['counter']) {
				break;
			}
			if (empty($parameters['id']) || $parameters['id'] == $programme['id']) {
				echo "Programme: " . $parameters['type'] . '-' . $programme['id'] . "\n";
				flush();
				$deliveryClass=  strtoupper($parameters['type']) . '_Delivery';
				// Get deliveries
				$deliveries =  $deliveryClass::get_programme_deliveries($programme['id'], $parameters['programme_session']);
				if (sizeof($deliveries) === 0) {
					continue;
				}
				// Kent
				$institution = '0122';
				// get campus
				$campus_id = $programme['campus_id'];
				$module_session = $this->parse_module_session($programme['module_session'], $parameters);
				echo "Module session: {$module_session}\n";
				if (empty($module_session)) {
					continue;
				}

				$module_cache =array();
				// cache modules for each delivery
				foreach ($deliveries as $delivery) {
					$cache_key = 'programme-modules.' . $parameters['type'] . '-' . $parameters['programme_session'] . '-' . base64_encode($delivery['pos_code']) . '-' . $programme['id'];

					if (array_key_exists($cache_key, $module_cache)) {
						continue;
					} else {
						$programme_modules_new = $this->load_module_data($parameters['type'], $programme['id'], $campus_id, $module_session);
						if ($programme_modules_new!==null) {
							$module_cache[$cache_key] = $programme_modules_new;
						}
					}

					$n_stages = ($programme_modules_new) ? count(get_object_vars($programme_modules_new)) : 0;
					echo "\nStages count:" . $n_stages . "\n\n";

					if (! $parameters['test_mode'] && $programme_modules_new!==null && $programme_modules_new!==false) {
						Cache::put($cache_key, $programme_modules_new, 2628000);
					}
					sleep($parameters['sleeptime']);
					flush();
				}
			}
		}
	}

	private function parse_module_session($module_session, $parameters)
	{
		if ($module_session != '') {
			// is the module session like a year
			if (preg_match('/^20[0-9][0-9]$/', $module_session)) {
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
			} elseif ($parameters['type'] == 'pg') {
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
		if (empty($arguments) || $arguments[0] == '-h') {
			$parameters['help'] = $this->help_argument();
			return $parameters;
		}

		// set defaults for the parameters in case they're not set
		$parameters = array();
		$parameters['programme_session'] = '2014';
		$parameters['sleeptime'] = 5;
		$parameters['counter'] = 1;
		$parameters['test_mode'] = false;

		foreach ($arguments as $argument) {
			$switch_name = substr($argument, 0, 2);
			switch ($switch_name) {
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
				case '-i':
					$parameters['id'] = str_replace('-i', '', $argument);
					break;
				case '-p':
					$parameters['type'] = 'pg';
					break;
				case '-u':
					$parameters['type'] = 'ug';
					break;
				default:
					$parameters['help'] = $this->help_argument();
			}
		}

		return $parameters;
	}


	public function help_argument()
	{
		return "
-s - programme session. Defaults to 2014.
-t - seconds per web service call. Defaults to 5 (one request every 5 seconds).
-c - programmes to process. Defaults to 1. 0 indicates all.
-x - test mode.
-i<id> - only process the single programme with id <id>. Requires specifying level with -u or -p.
-u - (with -i, otherwise ignored) the programme is an undergraduate programme
-p - (with -i, otherwise ignored) the programme is a postgraduate programme
\n\n";
	}

	/**
	 * fetch the modules structure for a given programe
	 * @param string $level - ug or pg
	 * @param int $pp_id - programmes plant id
	 * @param int $campus_id - the legacy campus id (1 for Canterbury etc)
	 * @param int $module_session - the academic year of the programme
	 * @param ProgrammesPlant\ModuleData $module - for testing
	 *
	 * @return stdClass module stages for the programme
	 */
	public function load_module_data($level, $pp_id, $campus_id, $module_session, $module = false)
	{
		if (empty($module_session)) {
			return false;
		}

		if (!$module) {
			$module = new ProgrammesPlant\ModuleData();
		}
		
		// build request
		$webservice_request = Config::get('module.api_base') . "/v1/programme-structure/$level/$pp_id/$campus_id/$module_session";
		
		// load data
		echo "Requesting: " . $webservice_request . ' - ';
		$data = $module->get_programme_modules($webservice_request);

	
		return $data;
	}
}

<?php

//require_once path('base') . 'vendor/autoload.php';

class XCRICAP_Task {

    /**
     * Generate the xcri-cap
     * 
     * @param array  $arguments The arguments sent to the moduledata command.
     */
    public function run($arguments = array())
    {

        $year = isset($arguments[0]) ? $arguments[0] : "current";

        $types = array('ug', 'pg');

        $api_index = array();
        $data = array();

        // add schools listing
        $data['schools'] = API::get_data('schools');
      
        // get a list of all our programmes through the API
        foreach ($types as $type) {

            $tmp_year = ($year == 'current') ? Setting::get_setting($type."_current_year") : $year;

            URLParams::$type = $type;
            URLParams::$year = $tmp_year;

            $api_index[$type] = API::get_index($tmp_year, $type);

            // fetch each programme individually for our xcri feed
            foreach ($api_index[$type] as $programme_id => $programme) {
                if ($programme['suspended'] == 'true' || $programme['withdrawn'] == 'true') {
                    continue;
                }
                $data['programmes'][$type][] = API::get_xcrified_programme($programme_id, $tmp_year, $type);
            }

            // get the global settings for our xcri feed
            $data['globals'][$type] = new StdClass();
            $globals = GlobalSetting::get_api_data($tmp_year) ;
            foreach ($globals as $key => $value) {
                $key = GlobalSetting::trim_id_from_field_name($key);
                $data['globals'][$type]->$key = $value;
            }
        }
        

        // if there are no programmes throw a 501 error
        if (empty($data))
        {
            echo "No programmes could be found!\n";
            return;
        }



        // if there are no global settings throw a 501 error
        if (! $data['globals']['ug'] ||  ! $data['globals']['pg']) 
        {
            echo "No global settings could be found!\n";
            return;
        }

        // assemble the xcri-cap xml
        $xcri_xml = View::make('xcri-cap.1-2', $data);

        // cache the xcri-cap xml before sending it
        $cache_key = "xcri-cap-{$year}";
        Cache::put($cache_key, $xcri_xml->__toString(), 2628000);

        echo "XCRIP-CAP for year:{$year} has been generated and stored in cache file: {$cache_key}\n";
    }
    
}
    
    
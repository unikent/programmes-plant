<?php

require_once path('base') . 'vendor/autoload.php';

class XCRICAP_Task {

    /**
     * Generate the xcri-cap
     * 
     * @param array  $arguments The arguments sent to the moduledata command.
     */
    public function run($arguments = array())
    {
        $year = isset($arguments[0]) ? $arguments[0] : "2014";

        $types = array('ug', 'pg');

        $api_index = array();
        $data = array();

        // get a list of all our programmes through the API
        foreach ($types as $type) {
            $api_index[$type] = API::get_index($year, $type);
            $api_index[$type] = API::get_index($year, $type);

            // fetch each programme individually for our xcri feed
            foreach (array_keys($api_index[$type]) as $programme_id) {
                $data['programmes'][$type][] = API::get_xcrified_programme($programme_id, $year, $type);
            }
        }
        

        // if there are no programmes throw a 501 error
        if (empty($data))
        {
            echo "No programmes could be found!\n";
            return;
        }

        // get the global settings for our xcri feed
        $globalsettings = GlobalSetting::get_api_data($year);

        // if there are no global settings throw a 501 error
        if (! $globalsettings)
        {
            echo "No global settings could be found!\n";
            return;
        }

        // neaten up the global settings
        $data['globalsettings'] = new StdClass();
        foreach ($globalsettings as $key => $value) {
            $key = GlobalSetting::trim_id_from_field_name($key);
            $data['globalsettings']->$key = $value;
        }

        // assemble the xcri-cap xml
        $xcri_xml = View::make('xcri-cap.1-2', $data);

        // cache the xcri-cap xml before sending it
        $cache_key = "xcri-cap-{$year}";
        Cache::put($cache_key, $xcri_xml->__toString(), 2628000);

        echo "XCRIP-CAP for {$year} has been generated\n";
    }
    
}
    
    
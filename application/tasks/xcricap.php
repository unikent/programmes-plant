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
        $level = isset($arguments[1]) ? $arguments[1] : "undergraduate";

        // get a list of all out programmes through the API
        $api_index = API::get_index($year, $level);

        $data = array();

        // fetch each programme individually for our xcri feed
        foreach (array_keys($api_index) as $programme_id) {
            $data['programmes'][] = API::get_xcrified_programme($programme_id, $year);
        }

        // if there are no programmes throw a 501 error
        if (! $data['programmes'])
        {
            return Response::make('', 501);
        }

        // get the global settings for our xcri feed
        $globalsettings = GlobalSetting::get_api_data($year);

        // if there are no global settings throw a 501 error
        if (! $globalsettings)
        {
            return Response::make('', 501);
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
        $cache_key = "xcri-cap-ug-$year";;
        Cache::put($cache_key, $xcri_xml->__toString(), 2628000);

        return $xcri_xml;
    }
    
}
    
    
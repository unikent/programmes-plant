<?php

class API_Controller extends Base_Controller 
{

    public $restful = true;
    
    public function __construct()
    {
        // turn off the profiler because this interferes with the web service
        Config::set('application.profiler', false);
    }
    
    /**
    * get the index data
    *
    * @param $year
    * @param $level - ug or pg
    * @return json data as a string or HTTP response
    */
    public function get_index($year, $level)
    {
        $path = path('storage') . 'api/' . $level . '/' . $year . '/';

        // 204 is the HTTP code for No Content - the result processed fine, but there was nothing to return.
        return file_exists($path . 'index.json') ? file_get_contents($path . 'index.json') : Response::error('204');
    }
    
    /**
    * get data for the programme
    *
    * @param $year
    * @param $level - ug or pg
    * @param $programme_id - the programme we're pulling data for
    * @return json data as a string or HTTP response    
    */
    public function get_programme($year, $level, $programme_id)
    {
        // set up the path to the output/cache file
        $path = path('storage') . 'api/' . $level . '/' . $year . '/';
        
        // try to get json files for global and programme settings, as well as the programme data itself
        // 204 is the HTTP code for No Content - the result processed fine, but there was nothing to return.
        if (! file_exists($path . 'globalsetting.json') or ! file_exists($path . 'programmesetting.json') or ! file_exists($path . $programme_id . '.json'))
        {
            return Response::error('204');
        }
        // if the cache files do exist for global/programme settings and the programme data, put them into objects
        $global_settings = json_decode(file_get_contents($path . 'globalsetting.json'));
        $programme_settings = json_decode(file_get_contents($path . 'programmesetting.json'));
        $programme = json_decode(file_get_contents($path . $programme_id . '.json'));
        
        // build up $final which will be an object with all the data in we need
        // start with the global settings
        $final = $global_settings;

        // now add programme settings to the $final object
        // no inhertence needed so just loop through the settings, adding them to the object
        foreach($programme_settings as $key => $value)
        {
            $final->{$key} = $value;
        }

        // pull in all programme dependencies eg an award id 1 will pull in all that award's data
        // loop through them, adding them to the $final object
        $programme = Programme::pull_external_data($programme);
        foreach($programme as $key => $value)
        {
            // make sure any existing key in the $final object gets updated with the new $value
            $final->{$key} = ( isset($final->{$key} ) && $value != '' ) ? $value : null;
        }

        // tidy up
        foreach(array('id','global_setting_id') as $key)
        {
            unset($final->{$key});
        }
        
        // now remove ids from our field names, they're not necessary
        // eg 'programme_id_1' becomes simply 'programme_id'
        $final = Programme::remove_ids_from_field_names($final);
        
        // return a json version of the newly-created $final object
        return Response::json($final);
    }

}
<?php

class Webservices_Controller extends Base_Controller 
{

    public $restful = true;

    public function get_index($year, $level)
    {
        $path = path('storage') . 'api/' . $level . '/' . $year . '/';

        // 204 is the HTTP code for No Content - the result processed fine, but there was nothing to return.
        return file_exists($path . 'index.json') ? file_get_contents($path . 'index.json') : Response::error('204');
    }

    public function get_programme($year, $level, $programme_id)
    {
        $path = path('storage') . 'api/' . $level . '/' . $year . '/';

        if (! file_exists($path . 'GlobalSetting.json') or ! file_exists($path . 'ProgrammeSetting.json'))
        {
            // 204 is the HTTP code for No Content - the result processed fine, but there was nothing to return.
            return Response::error('204');
        }

        $globals = json_decode(file_get_contents($path . 'GlobalSetting.json'));
        $settings = json_decode(file_get_contents($path . 'ProgrammeSetting.json'));

        if (! file_exists($path.$programme_id.'.json'))
        {
            return Response::error('404');
        }

        $programme = json_decode(file_get_contents($path . $programme_id . '.json'));

        // lets start with the globals
        $final = $globals;

        // Now add the programme globals
        // No inhertence needed so just do basic overwrite
        foreach($settings as $key => $value)
        {
            $final->{$key} = $value;
        }

        // now pull in all programme dependancies
        $programme = Programme::pull_external_data($programme);

        // finally, add the programme itself
        foreach($programme as $key => $value)
        {
            // Overwrite any duplicates with prog data (if prog data isn't blank)
            if (isset($final->{$key}) && $value != '') $final->{$key} = $value;
        }

        // tidy up
        foreach(array('id','global_setting_id') as $key)
        {
            unset($final->{$key});
        }

        // now remove ids from our field names, they're not necessary
        $final = Programme::remove_ids_from_field_names($final);

        return json_encode($final);
    }

}
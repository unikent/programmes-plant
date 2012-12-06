<?php

class Webservices_Controller extends Base_Controller 
{

    public $restful = true;

    public function get_index($year, $lvl)
    {
        $path = $GLOBALS['laravel_paths']['storage'].'api/'.$lvl.'/'.$year.'/';

        return file_exists($path . 'index.json') ? file_get_contents($path . 'index.json') : Response::error('404');
    }

    public function get_programme($year, $lvl, $programme_id)
    {
        $path = $GLOBALS['laravel_paths']['storage'].'api/'.$lvl.'/'.$year.'/';

        if(file_exists($path.$programme_id.'.json'))
        {
            $globals = json_decode(file_get_contents($path.'GlobalSetting.json'));
            $settings = json_decode(file_get_contents($path.'ProgrammeSetting.json'));
            $programme = json_decode(file_get_contents($path.$programme_id.'.json'));

            //lets start with the globals
            $final = $globals;

            //Now add the programme globals
            //No inhertence needed so just do basic overwrite
            foreach($settings as $key => $value){
                $final->{$key} = $value;
            }

            //now pull in all programme dependancies
            $programme = Programme::pull_external_data($programme);

            //finally, add the programme itself
            foreach($programme as $key => $value){
                //Overwrite any duplicates with prog data (if prog data isn't blank)
                if(isset($final->{$key}) && $value != '') $final->{$key} = $value;
            }

            //tidy up
            foreach(array('id','global_setting_id') as $key){
                unset($final->{$key});
            }

            //now remove ids from our field names, they're not necessary
            $final = Programme::remove_ids_from_field_names($final);

            return json_encode($final);

        }
        else
        {
            Response::error('404');
        }
    }

}
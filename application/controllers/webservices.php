<?php

class Webservices_Controller extends Base_Controller {

    public $restful = true;


    public function get_index($year,$lvl)
    {
         $path = $GLOBALS['laravel_paths']['storage'].'api/'.$lvl.'/'.$year.'/';

         echo file_exists($path.'index.json') ? file_get_contents($path.'index.json') : 'No json found';
         die();
    }

    public function get_programme($year,$lvl,$programme_id)
    {
        $path = $GLOBALS['laravel_paths']['storage'].'api/'.$lvl.'/'.$year.'/';

        if(file_exists($path.$programme_id.'.json')){

            $globals = json_decode(file_get_contents($path.'GlobalSetting.json'));
            $settings = json_decode(file_get_contents($path.'ProgrammeSetting.json'));
            $programme = json_decode(file_get_contents($path.$programme_id.'.json'));

            $final = $globals;

            //No inhertence needed so just do basic overwrite
            foreach($settings as $key => $value){
                $final->{$key} = $value;
            }
            foreach($programme as $key => $value){
                //Overwrite any duplicates with prog data (if prog data isn't blank)
                if(isset($final->{$key}) && $value != '') $final->{$key} = $value;
            }

            foreach(array('id','global_setting_id') as $key){
                unset($final->{$key});
            }

            echo json_encode($final);
            die();//return ended up with profiler still attached

        }else{

            echo "{'error':'none found'}";
        }
        
        die();
    }

}
<?php

class Modules {

    /**
    * get module data from SDS web service
    *
    * TODO - this is just a stub. It needs to be filled out with the full sds web service call (probably on a nightly cron)
    *
    * @param $year - not sure if we need this
    * @param $level - ug or pg
    * @param $pos_code
    * @param $pos_version - likely to be automated as the latest version on the sds side
    * @param $institution
    * @param $campus
    * @return json data as a string  
    */
    public function sds_modules($year, $level, $pos_code, $pos_version, $institution, $campus)
    {
        $json = '';
        // first get the current session from SDS
        // $session_code = 'https://ssdt-services.kent.ac.uk/services/public/organization?method=currentSession';
        $session_code = '2014';
        // Call the sds web service with the following params: $session_code, $pos_code, $pos_version, $institution, $campus
		return $json;
    }
    
}
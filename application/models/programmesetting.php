<?php

class ProgrammeSetting extends Revisionable
{
    public static $table = 'programme_settings';
    protected $revision_model = 'ProgrammeSettingRevision';
    protected $data_type_id = 'programme_setting';
    
    /**
     * Get the default setting for the specified column
     *
     * @param $year The year of the setting to retrieve
     * @param $colname column name of the setting to retrieve
     *
	 * @return $setting The specified setting as a string, or null if none is found
     */
    public static function get_setting($year, $colname){
    	$settings = ProgrammeSettingRevision::where('year', '=', $year)->where('status', '=', 'live')->get($colname);
    	
    	if(!empty($settings[0])){
    		return $settings[0]->$colname;
    	}
    	
    	return null;
    }

}

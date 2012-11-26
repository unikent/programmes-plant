<?php

class ProgrammeSetting extends Revisionable
{
    public static $table = 'programme_settings';
    public static $timestamps = true;
    public $revision = false;
    protected $revision_model = 'ProgrammeSettingRevision';
    protected $revision_type = 'programme_setting';
    protected $revision_table = 'programme_settings_revisions';

    
    /**
     * get the default setting for the specified column
     *
     * @param $year The year of the setting to retrieve
     * @param $colname column name of the setting to retrieve
     *
	 * @return $setting The specified setting as a string, or null if none is found
     */
    public static function get_setting($year, $colname){
    	$settings = self::where('year', '=', $year)->get($colname);
    	
    	if(!empty($settings[0])){
    		return $settings[0]->$colname;
    	}
    	
    	return null;
    }

}

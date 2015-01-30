<?php

abstract class ProgrammeSetting extends Revisionable
{
    public static $table = '';
    public static $revision_model = '';
    protected $data_type_id = 'programme_setting';

    public static $settings_cache = array();
    
    /**
     * Get the default setting for the specified column
     *
     * @param $year The year of the setting to retrieve
     * @param $colname column name of the setting to retrieve
     *
     * @return $setting The specified setting as a string, or null if none is found
     */
    public static function get_setting($year, $colname)
    {
        $revision_model = static::$revision_model;

        // If in memoery cache, return from there
        if(isset(static::$settings_cache[$revision_model][$year])) 
            return isset(static::$settings_cache[$revision_model][$year][$colname]) ? static::$settings_cache[$revision_model][$year][$colname] : '';

        // Otherwise, get data
        $settings = $revision_model::where('year', '=', $year)->where('status', '=', 'live')->first();
        
        // Check data was okay
        if(!$settings) return null;

        // Store in memoery cache & return
        static::$settings_cache[$year] = $settings->to_array();
        return isset($settings->$colname) ? $settings->$colname : '';    
    }

}
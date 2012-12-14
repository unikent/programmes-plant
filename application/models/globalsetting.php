<?php

class GlobalSetting extends Revisionable
{
    public static $table = 'global_settings';
    public static $timestamps = true;
    public $revision = false;
    protected $revision_model = 'GlobalSettingRevision';
    protected $revision_type = 'global_setting';
    protected $revision_table = 'global_settings_revisions';

    /**
     * Get the name of the 'institution name' field/column in the database.
     * 
     * @return The name of 'institution name' the  field.
     */
    public static function get_institution_name_field()
    {
        return 'institution_name_1';
    }

}

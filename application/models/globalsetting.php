<?php

class GlobalSetting extends Revisionable
{
    public static $table = 'global_settings';
    protected $revision_model = 'GlobalSettingRevision';
    protected $data_type_id = 'global_setting';

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

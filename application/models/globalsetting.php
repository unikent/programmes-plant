<?php

class GlobalSetting extends Revisionable
{
    public static $table = 'global_settings';
    public static $revision_model = 'GlobalSettingRevision';
    protected $data_type_id = 'global_setting';
}

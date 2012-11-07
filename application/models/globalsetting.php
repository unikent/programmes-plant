<?php

class GlobalSetting extends Revisionable {
    
    public static $table = 'global_settings';
    public static $timestamps = true;
    public $revision = false;
    protected $revision_type = 'global_setting';
    protected $revision_table = 'global_settings_revisions';
    
}
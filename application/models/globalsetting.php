<?php

class GlobalSetting extends Revisionable {

    public static $timestamps = true;
    public $revision = false;
    protected $revision_type = 'globalsetting';
    protected $revision_table = 'globals_settings_revisions';
    
}
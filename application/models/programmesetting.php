<?php

class ProgrammeSetting extends Revisionable
{
    public static $table = 'programme_settings';
    public static $timestamps = true;
    public $revision = false;
    protected $revision_model = 'ProgrammeSettingRevision';
    protected $revision_type = 'programme_setting';
    protected $revision_table = 'programme_settings_revisions';

}

<?php

class ProgrammeSettingFields_Controller extends Fields_Controller
{
    public $table = 'programme_settings';
    public $view = 'programmesettings';
    protected $model = 'ProgrammeField';
    protected $where_clause = array('isglobal', '=', true);
}
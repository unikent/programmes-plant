<?php

class ProgrammeSettingFields_Controller extends Fields_Controller
{
    public $table = 'programme_settings';
    public $view = 'programmesettings';
    protected $model = 'ProgrammeField';
    protected $where_clause;

    function __construct(){
    	parent::__construct();
    	$this->where_clause[] = array('programme_field_type', '=', ProgrammeField::$types['OVERRIDABLE_DEFAULT']); 
    	$this->where_clause[] = array('programme_field_type', '=', ProgrammeField::$types['DEFAULT']);
    }
}
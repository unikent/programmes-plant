<?php

class ProgrammeFields_Controller extends Fields_Controller
{
    public $table = 'programmes';
    public $view = 'programmes';
    protected $model = 'ProgrammeField';
    public $name = 'Programmes';
    protected $where_clause;

    function __construct(){
    	parent::__construct(); 
    	$this->where_clause[] = array('programme_field_type', '=', ProgrammeField::$types['NORMAL']);
    	$this->where_clause[] = array('programme_field_type', '=', ProgrammeField::$types['OVERRIDABLE_DEFAULT']);
    }
}

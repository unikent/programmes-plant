<?php

class ProgrammeFields_Controller extends Fields_Controller
{
    public $table = 'programmes';
    public $view = 'programmes';
    protected $model = 'ProgrammeField';
    public $name = 'Programmes';
    protected $where_clause = array('isglobal', '=', false);
}

<?php

class ProgrammeFields_Controller extends Fields_Controller {

	public $table = 'programmes';
	public $view = 'programmes';
	protected $model = 'ProgrammeField';
	public $name = 'Programmes';
	protected $where_clause;

	function __construct(){
		$fieldModel = URLParams::get_type()."_ProgrammeField";
		parent::__construct(); 
		$this->where_clause[] = array('programme_field_type', '=', $fieldModel::$types['NORMAL']);
		$this->where_clause[] = array('programme_field_type', '=', $fieldModel::$types['OVERRIDABLE_DEFAULT']);
		
		$type = URI::segment(1);
		$this->model = $type.'_'.$this->model;

	}

}

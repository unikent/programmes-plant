<?php

class ProgrammeSettingFields_Controller extends Fields_Controller {
	public $table = 'programme_settings';
	public $view = 'programmesettings';
	protected $model = 'ProgrammeField';
	public $name = 'ProgrammeSettings';
	protected $where_clause;

	function __construct()
	{
		parent::__construct();
		$this->where_clause[] = array('programme_field_type', '=', ProgrammeField::$types['DEFAULT']);
	}
}
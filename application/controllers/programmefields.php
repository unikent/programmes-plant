<?php

class ProgrammeFields_Controller extends Fields_Controller {

	public $name = 'Programmes';
	public $view = 'standard';

	function __construct()
	{
		$type = URLParams::get_type();
		$model = $this->model = $type.'_ProgrammeField';
		
		parent::__construct(); 
	}

}

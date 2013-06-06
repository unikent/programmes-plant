<?php

class Editor_Controller extends Admin_Controller {
	
	public $restful = true;

	public $required_permissions = array('recieve_edit_requests');

	public function __construct()
	{  	
		$this->model = (URI::segment(2)=='ug') ? 'UG_Programme' : 'PG_Programme';
		// Construct parent.
		parent::__construct();
	}

	public function get_inbox()
	{	
		$model = $this->model;
		return $this->layout->nest('content', 'admin.editor.index', array('for_review' => $model::get_under_review()));
	}

}
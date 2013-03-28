<?php

class Editor_Controller extends Admin_Controller {
	
	public $restful = true;

	public $required_permissions = array('recieve_edit_requests');

	public function get_inbox()
	{
		return $this->layout->nest('content', 'admin.editor.index', array('for_review' => Programme::get_under_review()));
	}

}
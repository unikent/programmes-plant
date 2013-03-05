<?php

class Admin_Controller extends Base_Controller {

	public $layout = 'layouts.admin';

	public $required_permissions = array();

	public function __construct()
	{  	
		//Check auth for any controllers using Admin_controller
		//this should include everything other than the auth page itself
		if (Request::env() != 'test')
		{
			$this->filter('before', 'auth', array($this->required_permissions));
		}

		// Default variable set for CRUD usage.
		$this->data['create'] = false;

		$this->data['type'] = $this->views;

		// Construct parent.
		parent::__construct();
	}
	
}
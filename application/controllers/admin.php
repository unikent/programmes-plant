<?php

class Admin_Controller extends Base_Controller {

	public $layout = 'layouts.admin';

	public $required_permissions = array();

	public function __construct()
	{  	
		//Check auth for any controllers using Admin_controller
		//this should include everything other than the auth page itself
		$this->filter('before', 'auth', array($this->required_permissions));

		// Default variable set for CRUD usage.
		$this->data['create'] = false;

		$this->data['type'] = $this->views;

		// Construct parent.
		parent::__construct();
	}

	/**
	 * Show a message when the user has insufficient permissions to perform a particular action.
	 * 
	 * @param array|string  $permissions  Single permissions string or array of permissions the user doesn't have, but needs to perform the action.
	 * @return View         A view telling the user about the error message.
	 */
	protected function insufficient_permissions($permissions)
	{
		return $this->layout->nest('content', 'admin.inc.no_permissions', array("perms" => (array)$permissions));
	}
	
}
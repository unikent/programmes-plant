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

	/*
	 * Ensure that the current user has valid permissions to perform the specified action. If not, output an error and die.
	 *
	 * @param $permissions array|string specifying permission required
	 * @return void
	 */
	protected function check_user_can($permissions)
	{
		// If a user does not have the required permission
		if(!Auth::user()->can($permissions)){
			if(!is_array($permissions)) $permissions = array($permissions);
			// If user is not able to view/use this method, force output of page and die.
			$this->layout->nest('content', 'admin.inc.no_permissions', array("perms" => $permissions));

			echo Response::make($this->layout)->render();
			
			// Stop the framework
			die();
		}
	}
	
}
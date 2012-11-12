<?php

class Admin_Controller extends Base_Controller {

	public $layout = 'layouts.admin';

    public function __construct()
    {  
        // Default variable set for CRUD usage.
    	$this->data['create'] = false;

		$this->data['type'] = $this->views;

    	// Construct parent.
    	parent::__construct();
    }
    
}
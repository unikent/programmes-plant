<?php

class Admin_Controller extends Base_Controller {

    // Our first stuff
    public function __construct(){  
        // Default variable set for CRUD usage.
    	$this->data['create'] = false;
    }
}
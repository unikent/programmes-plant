<?php

class Admin_Controller extends Base_Controller {

    // Our first stuff
    public function __construct(){  

        // dev environment uses a test user without LDAP
        if (Request::is_env('local')) {
            $this->data['user'] = new stdClass();
            $user = Config::get('auth.testuser');
            $this->data['user']->id = $user['id'];
            $this->data['user']->username = $user['username'];
            $this->data['user']->name = $user['name'];
            $this->data['user']->fullname = $user['fullname'];
            $this->data['user']->email = $user['email'];
            $this->data['user']->title = $user['title'];
            $this->data['user']->dept = $user['dept'];
            $this->data['user']->isadmin = $user['isadmin'];
            $this->data['user']->isuser = $user['isuser'];
        }
        else {
            // Make sure that the 'auth' function is run before ANYTHING else happens
            // With the exception of our login page (to actually login) this will
            // prevent any un-authorised use of the admin areas
            $this->filter('before', 'auth')->except(array('login'));

            // Get the user details from when they logged in / old sessions
            $this->data['user'] = Auth::user();
        }

        // Default variable set for CRUD usage.
    	$this->data['create'] = false;

        
    }

    /**
     * Checks to see if the user is logged in. If they aren't we get redirected to the login page
     * @return header redirect
     */
		public function auth() {
		echo 'x';exit;
			if (Auth::guest()) return Redirect::to('login');
    }

}
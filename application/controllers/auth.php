<?php

class Auth_Controller extends Base_Controller {

	public $restful = true;

	public function get_login()
	{
		if (Auth::guest())
		{
			if (Session::has('referrer'))
			{
				Redirect::to(Session::get('referrer'));
			}
		}

		return View::make('admin.login', $this->data);
	}

	/**
	 * Logout: log user out of the system
	 */
	public function get_logout()
	{
		// Kill the login - session is flushed at the same time.
		Auth::logout();

		Session::flash('flash', 'Logged out');

		return Redirect::to('login');
	}

	/**
	 * Login: Attempt to authenticate a user
	 */
	public function post_login()
	{
		// If username is valid
		if (ctype_alnum(Input::get('username')))
		{	
			// Attempt to log user in. For now just tell the user the exception message they got if it fails.
			try {
				Auth::attempt(array('username' => Input::get('username'), 'password' => Input::get('password')));
			} catch (Exception $e){
				Session::flash('flash', 'Login failed: '.$e->getMessage());
				return Redirect::to('login');
			}
			// If user has a referrer, send them there
			if(Session::has('referrer'))
			{
				return Redirect::to(Session::get('referrer'));
			}
			// else send em to the index
			return Redirect::to('/');
		}
		else
		{
			return Redirect::to('login');
		}
	}

}
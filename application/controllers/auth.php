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

		// Re: the str_replace
		// Allow authentication using test users (Normal username -test). Test accounts will authenticate normally
		// using the parent usernames credentals, but can be setup in the admin backend to have custom permissions.

		// If username is valid
		if (ctype_alnum(str_replace('-test','',Input::get('username'))))
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
			Session::flash('flash', 'Login failed: Invalid username');
			return Redirect::to('login');
		}
	}

}
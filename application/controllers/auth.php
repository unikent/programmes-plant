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

	public function get_logout()
	{
		// Kill the login - session is flushed at the same time.
		Auth::logout();

		Session::flash('status', 'Logged out');

		return Redirect::to('login');
	}

	public function post_login()
	{
		if (ctype_alnum(Input::get('username')) && Auth::attempt(array('username' => Input::get('username'), 'password' => Input::get('password'))))
		{
			if(Session::has('referrer'))
			{
				return Redirect::to(Session::get('referrer'));
			}
			
			return Redirect::to('/');
		}
		else
		{
			return Redirect::to('login');
		}
	}

}
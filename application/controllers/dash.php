<?php
class Dash_Controller extends Admin_Controller
{
    
    public $restful = true;

    public function get_index()
    {   
        $this->data['programmes'] =  Programme::where('year','=',URI::segment(1))->get();
        return View::make('admin.index',$this->data);
    }

    // Login Stuff
    public function get_login(){
    	return View::make('admin.login', $this->data);
    }
    public function get_logout(){
    	Auth::logout();
		return Redirect::to('login');
    }

    public function post_login(){
        if (Auth::attempt(array('username'=>Input::get('username'), 'password'=>Input::get('password'))))
        {
            if(Session::has('referrer')){
                return Redirect::to(Session::get('referrer'));
            }
            return Redirect::to('/');
        }else{
            return Redirect::to('login');
        }
    }
    
}
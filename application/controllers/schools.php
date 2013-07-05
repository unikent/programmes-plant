<?php
class Schools_Controller extends Simple_Admin_Controller {

	public $restful = true;
	public $views = 'schools';
	protected $model = 'School';
	public $custom_form = true;

	public function post_create()
	{
		$model = $this->model;

		if (! $model::is_valid())
		{
			Messages::add('error', $model::$validation->errors->all());
			return Redirect::to($this->views.'/create')->with_input();
		}
		
		$school = new School;
		$school->input();
		$school->save();
 
		Messages::add('success','New School Added');
		return Redirect::to($this->views.'');
	}

	public function post_edit()
	{
		$model = $this->model;

		if (! $model::is_valid())
		{
			Messages::add('error', $model::$validation->errors->all());
			return Redirect::to($this->views.'/edit/'.Input::get('id'));
		}else{
			$school = School::find(Input::get('id'));
   
			$school->name = Input::get('name');
			$school->faculties_id = Input::get('faculty');

			$school->save();

			Messages::add('success','School updated');
			return Redirect::to($this->views.'');
		}
	}

}
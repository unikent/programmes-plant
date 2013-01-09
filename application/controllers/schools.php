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
			Messages::add('error', $validation->errors->all());
			return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'/create')->with_input();
		}
		
		$school = new School;
		$school->input();
		$school->save();
 
		Messages::add('success','New School Added');
		return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'');
	}

	public function post_edit()
	{
		
		$rules = array(
			'id'  => 'required|exists:schools',
			'name'  => 'required|max:255|unique:schools,name,'.Input::get('id'),
			'faculty'  => 'required|exists:faculties,id'
		);
		
		$validation = Validator::make(Input::all(), $rules);

		if ($validation->fails())
		{
			Messages::add('error',$validation->errors->all());
			return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'/edit/'.Input::get('id'));
		}else{
			$school = School::find(Input::get('id'));
   
			$school->name = Input::get('name');
			$school->faculties_id = Input::get('faculty');

			$school->save();

			Messages::add('success','School updated');
			return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'');
		}
	}

}
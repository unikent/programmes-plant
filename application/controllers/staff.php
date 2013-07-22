<?php
class Staff_Controller extends Simple_Admin_Controller {
	public $restful = true;
	public $views = 'staff';
	public $model = 'Staff';
	public $custom_form = true;


	/**
	 * Create a new item via POST.
	 */
	public function post_create()
	{
		$model = $this->model;

		$rules = array(
			'login'  => 'required|max:255',
		);

		if (! $model::is_valid($rules))
		{
			Messages::add('error', $model::$validation->errors->all());
			Input::flash();//Save previous inputs to avoid blanking form.
			return Redirect::to($this->views.'/create')->with_input();
		}

		$new = new $this->model;

		$new->populate_from_input();

		$new->save();

		Messages::add('success', __($this->l . 'success.create'));

		return Redirect::to($this->views.'');
	}

	/**
	 * Edit an item via POST.
	 */
	public function post_edit()
	{
		$model = $this->model;
		
		$rules = array(
			'id'  => 'required|exists:'. $model::$table .',id',
			'login'  => 'required|max:255',
		);

		if (! $model::is_valid($rules))
		{
			Messages::add('error', $model::$validation->errors->all());
			Input::flash();//Save previous inputs to avoid blanking form.
			return Redirect::to($this->views.'/edit/'.Input::get('id'));
		}

		$update = $model::find(Input::get('id'));
		$update->populate_from_input();

		$update->save();

		Messages::add('success', __($this->l . 'success.edit'));
		return Redirect::to($this->views.'');
	}	
	
}
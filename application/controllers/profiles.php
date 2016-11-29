<?php
class Profiles_Controller extends Simple_Admin_Controller {
	public $restful = true;
	public $views = 'profile';
	public $model = '';
	public $level = null;
	public $custom_form = true;
	public $shared_data = false;

	// Determine correct model (PG / UG)
	public function __construct()
	{

		$this->level = URI::segment(1);
		$this->model = ($this->level=='ug') ? 'UG_Profile' : 'PG_Profile';

		// Construct parent.
		parent::__construct();
	}

	/**
	 * Return all data and send to an index view.
	 */
	public function get_index()
	{
		$model = $this->model;
	//	$this->data['shared'] = $this->shared_data;
		$this->data['items'] = $model::all_active('course')->get();
		$this->layout->nest('content', 'admin.profile.index', $this->data);
	}

	/**
	 * Create a new item via POST.
	 */
	public function post_create()
	{
		$model = $this->model;

		$rules = array(
			'name' => 'required|max:255'
		);

		if (! $model::is_valid($rules))
		{
			Messages::add('error', $model::$validation->errors->all());
			Input::flash();//Save previous inputs to avoid blanking form.
			return Redirect::to($this->level . '/' . $this->views.'/create')->with_input();
		}

		$cats = is_array(Input::get('subject_categories')) ? implode(',', array_filter(Input::get('subject_categories'))) : '';

		Input::merge(array('subject_categories' =>  $cats));

		$new = new $this->model;

		$new->populate_from_input();

		$new->save();

		Messages::add('success', __($this->l . 'success.create'));

		return Redirect::to($this->level . '/' . $this->views.'');
	}

	/**
	 * Edit an item via POST.
	 */
	public function post_edit()
	{
		$model = $this->model;

		$rules = array(
			'id'  => 'required|exists:'. $model::$table .',id',
			'name'  => 'required|max:255',
		);

		if (! $model::is_valid($rules))
		{
			Messages::add('error', $model::$validation->errors->all());
			Input::flash();//Save previous inputs to avoid blanking form.
			return Redirect::to($this->level . '/' . $this->views.'/edit/'.Input::get('id'));
		}

		$cats = is_array(Input::get('subject_categories')) ? implode(',', array_filter(Input::get('subject_categories'))) : '';

		Input::merge(array('subject_categories' =>  $cats));

		$update = $model::find(Input::get('id'));
		$update->populate_from_input();

		$update->save();

		Messages::add('success', __($this->l . 'success.edit'));
		return Redirect::to($this->level . '/' . $this->views.'');
	}

}

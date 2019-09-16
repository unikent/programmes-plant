<?php
class Profiles_Controller extends Simple_Admin_Controller {
	public $restful = true;
	public $views = 'profile';
	public $model = 'Profile';
	public $custom_form = true;
	public $shared_data = false;


	/**
	 * Return all data and send to an index view.
	 */
	public function get_index()
	{
		$model = $this->model;
		$this->data['shared'] = $this->shared_data;
		$this->data['items'] = $model::all_active('course')->get();
		$this->layout->nest('content', 'admin.profile.index', $this->data);
	}

	public function get_export()
	{
		$model = $this->model;
		// create a file pointer connected to the output stream
		$output = fopen('php://output', 'w');
		header('content-type: text/csv');
		header('charset:utf-8');
		fputcsv($output, array(
			'Name',
			'Slug',
			'Course',
			'Subject Categories',
			'Video',
			'Type',
			'Created At',
			'Updated At',
			'Links',
			'Quote',
			'Lead',
			'Content'
		));
		foreach($model::all_active('course')->get() as $profile) {
			fputcsv($output, array(
				$profile->attributes['name'],
				$profile->slug,
				$profile->course,
				$profile->subject_categories,
				$profile->video,
				$profile->type,
				$profile->created_at,
				$profile->updated_at,
				$profile->links,
				$profile->quote,
				$profile->lead,
				$profile->content,
			));
		}
		exit();
	}

	/**
	 * Create a new item via POST.
	 */
	public function post_create()
	{
		$model = $this->model;
		$url = $this->get_base_page();

		$rules = array(
			'name' => 'required|max:255'
		);

		if (! $model::is_valid($rules))
		{
			Messages::add('error', $model::$validation->errors->all());
			Input::flash();//Save previous inputs to avoid blanking form.
			return Redirect::to($url.'/create')->with_input();
		}

		$cats = is_array(Input::get('subject_categories')) ? implode(',', array_filter(Input::get('subject_categories'))) : '';

		Input::merge(array('subject_categories' =>  $cats));

		$new = new $this->model;

		$new->populate_from_input();

		$new->save();

		Messages::add('success', __($this->l . 'success.create'));

		return Redirect::to($url.'/edit/'.$new->id);
	}

	/**
	 * Edit an item via POST.
	 */
	public function post_edit()
	{
		$model = $this->model;
		$url = $this->get_base_page();

		$rules = array(
			'id'  => 'required|exists:'. $model::$table .',id',
			'name'  => 'required|max:255',
		);

		if (! $model::is_valid($rules))
		{
			Messages::add('error', $model::$validation->errors->all());
			Input::flash();//Save previous inputs to avoid blanking form.
			return Redirect::to($url . '/edit/' . Input::get('id'));
		}

		$cats = is_array(Input::get('subject_categories')) ? implode(',', array_filter(Input::get('subject_categories'))) : '';

		Input::merge(array('subject_categories' =>  $cats));

		$update = $model::find(Input::get('id'));
		$update->populate_from_input();

		$update->save();

		Messages::add('success', __($this->l . 'success.edit'));
		return Redirect::to($url.'/edit/'.$update->id);
	}

}

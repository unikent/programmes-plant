<?php
class Images_Controller extends Simple_Admin_Controller {

	public $restful = true;
	public $views = 'images';
	public $model = 'Image';
	public $custom_form = true;

	/**
	 * Return all data and send to an index view.
	 */
	public function get_index()
	{
		$model = $this->model;

		$this->data['items'] = $model::all_active('name')->get();
		$this->data['shared'] = $this->shared_data;
		$this->layout->nest('content', 'admin.images.index', $this->data);
	}


	public function post_create()
	{
		$model = $this->model;
		$url = $this->get_base_page();

		if (! $model::is_valid($rules))
		{
			Messages::add('error', $model::$validation->errors->all());
			Input::flash();//Save previous inputs to avoid blanking form.
			return Redirect::to($url.'/create');//->with_input();
		}
	
		$new = new $this->model;

		$new->name = Input::get('name');
		$new->populate_from_input();
		$new->save();

		$img = Input::file('image');
		if(isset( $img['error']) && $img['error'] === 0){
			$u = Input::upload('image', path('storage').'images', $new->id.'.jpg');
		}
		
		Messages::add('success', __($this->l . 'success.create'));

		return Redirect::to($url);
	}

		/**
	 * Edit an item via POST.
	 */
	public function post_edit()
	{
		$model = $this->model;
		$url = $this->get_base_page();

		$id = Input::get('id');
		
		$rules = array(
			'id'  => 'required|exists:'. $model::$table .',id'
		);

		if (! $model::is_valid($rules))
		{
			Messages::add('error', $model::$validation->errors->all());
			Input::flash();//Save previous inputs to avoid blanking form.
			return Redirect::to($url . '/edit/' . $id);
		}

		$update = $model::find($id);

		$update->name = Input::get('name');
		$update->populate_from_input();

		$update->save();

		$img = Input::file('image');
		if(isset( $img['error']) && $img['error'] === 0){
			$u = Input::upload('image', path('storage').'images', $update->id.'.jpg');
		}

		Messages::add('success', __($this->l . 'success.edit'));
		
		return Redirect::to($url);
	}


}
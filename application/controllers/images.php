<?php
class Images_Controller extends Simple_Admin_Controller {

	public $restful = true;
	public $views = 'images';
	public $model = 'Image';
	public $custom_form = true;


	public function post_create()
	{
		$model = $this->model;
		$url = $this->get_base_page();


		$rules = array(
			'name'  => 'required|unique:' . $model::$table . '|max:255',
		);

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

		if(Input::has('image')){
			Input::upload('image', path('storage').'images', $new->id.'.jpg');
		}
		
		Messages::add('success', __($this->l . 'success.create'));

		return Redirect::to($url);
	}


}
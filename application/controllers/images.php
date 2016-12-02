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

		$this->data['items'] = $model::all_active('created_at')->get();
		$this->data['shared'] = $this->shared_data;
		$this->layout->nest('content', 'admin.images.index', $this->data);
	}

	/**
	 * Create an image via POST.
	 */
	public function post_create()
	{
		$model = $this->model;
		$url = $this->get_base_page();

		if (! $model::is_valid())
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
			$this->save_image($new->id);
		}
		
		Messages::add('success', __($this->l . 'success.create'));

		return Redirect::to($url);
	}

	/**
	 * Edit an image via POST.
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
			$this->save_image($update->id);
		}

		Messages::add('success', __($this->l . 'success.edit'));
		
		return Redirect::to($url);
	}

	/**
	 * Save image - uploads image, saves it & generates a thumbnail
	 */
	public function save_image($img_id){
		$path = path('storage').'images';
		$u = Input::upload('image', path('storage').'images', $img_id.'.jpg');

		if($u){
			$this->make_thumb( path('storage').'images/'.$img_id.'.jpg', path('storage').'images/'.$img_id.'_thumb.jpg', 160);
		}

		return $u;
	}

	// see: https://davidwalsh.name/create-image-thumbnail-php
	protected function make_thumb($src, $dest, $desired_width) {

		/* read the source image */
		$source_image = imagecreatefromjpeg($src);
		$width = imagesx($source_image);
		$height = imagesy($source_image);
		
		/* find the "desired height" of this thumbnail, relative to the desired width  */
		$desired_height = floor($height * ($desired_width / $width));
		
		/* create a new, "virtual" image */
		$virtual_image = imagecreatetruecolor($desired_width, $desired_height);
		
		/* copy source image at a resized size */
		imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
		
		/* create the physical thumbnail image to its destination */
		imagejpeg($virtual_image, $dest);
	}

}
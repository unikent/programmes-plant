<?php
class Images_Controller extends Simple_Admin_Controller {

	public $restful = true;
	public $views = 'images';
	public $model = 'Image';
	public $custom_form = true;
	/**
	 * Array to store headers as header => value.
	 *
	 * Static so that potentially other classes could arbitarily add or modify headers here.
	 */
	public static $headers = array();

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

		$rules = array(
			'image' => 'required|mimes:jpg|max:1000'
		);

		if (! $model::is_valid($rules))
		{
			Messages::add('error', $model::$validation->errors->all());
			Input::flash();//Save previous inputs to avoid blanking form.
			return Redirect::to($url.'/create');//->with_input();
		}

		$img = Input::file('image');
		$size = getimagesize($img['tmp_name']);

		$new = new $this->model;

		$new->name = Input::get('name');
		$new->populate_from_input();
		$new->height = $size[1];
		$new->width = $size[0];
		$new->save();

		$img = Input::file('image');

		if(isset( $img['error']) && $img['error'] === 0){
			if(!$this->save_image($new->id)){
				$new->raw_delete();
			}
		}else{
			$new->raw_delete();
		}
		
		Messages::add('success', __($this->l . 'success.create'));

		return Redirect::to($url.'/edit/'.$new->id);
	}
	/**
	 * Edit an image via POST.
	 */
	public function post_edit()
	{
		$model = $this->model;
		$url = $this->get_base_page();
		$img = Input::file('image');

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

		// If new image was submitted, update width/height then save it
		if(isset( $img['error']) && $img['error'] === 0){
			$size = getimagesize($img['tmp_name']);
			$update->height = $size[1];
			$update->width = $size[0];
			$this->save_image($update->id);
		}

		$update->save();

		API::purge_output_cache();

		Messages::add('success', __($this->l . 'success.edit'));
		
		return Redirect::to($url.'/edit/'.$update->id);
	}

	/**
	 * Save image - uploads image, saves it & generates a thumbnail
	 */
	public function save_image($img_id){

		$path = Config::get('images.image_directory', path('storage').'images');

		$u = Input::upload('image', $path, $img_id.'.jpg');

		if($u){
			$this->make_thumb( $path.'/'.$img_id.'.jpg', $path.'/'.$img_id.'_thumb.jpg', 160);
		}

		return $u;
	}

	/**
	 * upload image - called from redactor
	 */
	public function post_upload(){

		$model = $this->model;

		$rules = array(
			'image' => 'required|mimes:jpg|max:1000',
			'name' => ''
		);

		if(!$model::is_valid($rules)){
			return static::json(array('error'=>'invalid input','errors'=> $model::$validation->errors->all()), 422);
		}

		$img = Input::file('image');
		if(isset( $img['error']) && $img['error'] === 0){
			$name = explode('.',$img['name']);
			array_pop($name);
			$name = implode('.',$name);
		}else{
			return static::json(array('error'=>'invalid input'), 422);
		}

		$size = getimagesize($img['tmp_name']);
	
		$new = new $this->model;
		$new->name = $name;
		$new->height = $size[1];
		$new->width = $size[0];
		$new->populate_from_input();

		$new->save();

		$img = Input::file('image');
		if(isset( $img['error']) && $img['error'] === 0){
			if(!$this->save_image($new->id)){
				$new->raw_delete();
			}
		}else{
			$new->raw_delete();
		}

		return static::json(API::get_data_single('image',$new->id));
	}

	/**
	 * Output as JSON
	 *
	 * @param mixed $data        To be shown as JSON.
	 * @param int   $code        HTTP code to return.
	 * @param array $add_headers Additional headers to add to output.
	 */
	public static function json($data, $code = 200, $add_headers = false)
	{
		static::$headers['Content-Type'] = 'application/json';

		if ($add_headers)
		{
			static::$headers = array_merge(static::$headers, $add_headers);
		}

		// Add access controls to allow JS to talk
		static::$headers['Access-Control-Allow-Origin'] = '*';

		// Add JSONP support (if callback param)
		if (isset($_GET['callback']))
		{
			return Response::jsonp($_GET['callback'], $data, $code, static::$headers);
		}
		else
		{
			return Response::json($data, $code, static::$headers);
		}

	}

	// see: https://davidwalsh.name/create-image-thumbnail-php
	protected function make_thumb($src, $dest, $desired_width) {

		/* read the source image */
		$source_image = imagecreatefromjpeg($src);
		$width = imagesx($source_image);
		$height = imagesy($source_image);
		
		// Get thumb sizeing
		list($desired_height, $desired_width) = Image::getThumbSize($desired_width, $width, $height);
		/* create a new, "virtual" image */
		$virtual_image = imagecreatetruecolor($desired_width, $desired_height);
		
		/* copy source image at a resized size */
		imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
		
		/* create the physical thumbnail image to its destination */
		imagejpeg($virtual_image, $dest);
	}

}
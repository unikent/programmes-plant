<?php
/**
 * Simple_Admin_Controller
 * 
 * Provides a simple interface to single line admin functions.
 * 
 * We have a number of these: awards, schools and so on.
 */
class Simple_Admin_Controller extends Admin_Controller {

	// Whether to use a custom form here
	public $custom_form = false;
	// Is data shared between UG & PG (if false data is treated as being seperate using UG_* vs PG_* models)
	public $shared_data = true;
	// Requered permission
	public $required_permissions = array("edit_data");

	// Stores the shortcut variable for the language file
	protected $l = '';

	/**
	 * Setup controller actions
	 *
	 */
	public function __construct()
	{
		// Quick use variable for access to language files
		$this->l = $this->views . '.';

		// Prefix model if data model isn't shared
		if(!$this->shared_data) $this->model = URLParams::$type.'_'.$this->model;

		// Construct parent.
		parent::__construct();
	}

	/**
	 * Return all data and send to an index view.
	 */
	public function get_index()
	{
		$model = $this->model;

		$this->data['items'] = $model::all_active()->get();
		$this->data['shared'] = $this->shared_data;
		$this->layout->nest('content', 'admin.indexes.simple-index', $this->data);
	}
	
	/**
	 * Display an edit form for an item.
	 */
	public function get_edit()
	{
		// Get last paramater (the id)
		$params = func_get_args();
		$object_id = end($params);

		// get site url
		$url = $this->get_base_page();

		if (! $object_id)
		{
			Messages::add('error', "No " . Str::lower($this->model) . "ID provided, so could not be loaded.");
			return Redirect::to($url);
		}

		$model = $this->model;

		$object = $model::find($object_id);

		if (! $object)
		{
			Messages::add('error', __($this->l . 'error.not_found'));
			return Redirect::to($this->views);
		}

		$this->data['item'] = $object;
		$this->data['shared'] = $this->shared_data;
		
		if ($this->custom_form)
		{
			$this->layout->nest('content', 'admin.'.$this->views.'.form', $this->data);
		}
		else
		{
			$this->layout->nest('content', 'admin.generic-forms.single-field', $this->data);
		}
	}

	/**
	 * Show a creation form for an item.
	 */
	public function get_create()
	{
		$this->data['create'] = true;
		$this->data['shared'] = $this->shared_data;

		if ($this->custom_form)
		{
			$this->layout->nest('content', 'admin.'.$this->views.'.form',$this->data);
		}
		else
		{
			$this->layout->nest('content', 'admin.generic-forms.single-field', $this->data);
		}
	}

	/**
	 * Route for deletion of an item.
	 */
	public function get_delete()
	{
		// Get last paramater (the id)
		$params = func_get_args();
		$id = end($params);
		
		// get site url
		$url = $this->get_base_page();

		$model = $this->model;

		$rules = array(
			'id'  => 'required|integer|exists:' . $model::$table,
		);

		//Don't call core validator method or deletes will never pass (since they only ever have id)
		if (!  Validator::make(array('id' => $id), $rules)->passes())
		{
			Messages::add('error', __($this->l . 'error.delete'));

			return Redirect::to($url);
		}
		else
		{
			$remove = $model::find($id);
			$remove->delete();

			Messages::add('success', __($this->l . 'success.delete'));
			return Redirect::to($url);
		}
	}

	/**
	 * Create a new item via POST.
	 */
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
			return Redirect::to($url.'/create')->with_input();
		}

		$new = new $this->model;

		$new->name = Input::get('name');
		$new->populate_from_input();
		$new->save();

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
			'id'  => 'required|exists:'. $model::$table .',id',
			'name'  => 'required|max:255|unique:'. $model::$table . ',name,' . $id
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

		Messages::add('success', __($this->l . 'success.edit'));
		
		return Redirect::to($url);
	}

	/**
	 * Get path to current "base" page
	 * - ug/type or /type depending on if shared
	 */
	private function get_base_page(){
		$prefix = (!$this->shared_data) ? URLParams::$type.'/' : '';
		return $prefix.$this->views;
	}

}
<?php
use \Verify\Models\Permission;

abstract class Fields_Controller extends Admin_Controller {

	public $restful = true;

	public $required_permissions = array('configure_fields');

	/**
	 * Display the fields index page.
	 */
	public function get_index()
	{
		$model = $this->model;

		// Sections
		$sections = null;

		// Get immutable fields if view is immutable
		if($this->view == 'immutable')
		{
			$fields = $model::order_by('field_name','asc')->get();
		}
		else
		{
			// Get programmes fields + sections
			$sectionModel = $model::$sections_model;
			$fields = $model::order_by('order','asc')->get();
			$sections = $sectionModel::order_by('order','asc')->get();
		}

		$this->layout->nest(
			'content', 
			'admin.fields.'.$this->view,
			 array(
			 	'fields' => $fields,
			  	'sections' => $sections,
			  	'path' => URI::current()
			)
		);
	}

	public function get_add()
	{
		$data = array(
			'path' => URI::current(),
			'roles' => Role::all(true),
			'permissions' => array('R' => array(), 'W' => array()),
			'model' => $this->model,
			'field_type' => $this->view,
			'type' => URLParams::get_type($this->model),
		);

		$this->layout->nest('content', 'admin.fields.form', $data);
	}

	public function get_edit()
	{	
		// Get ID of item being edited
		$params = func_get_args();
		$id = end($params);

		$model = $this->model;
		$field = $model::find($id);

		$data = array(
			'path' => URI::current(),
			'type' => URLParams::get_type($this->model),
			'model' => $model,
			'id' => $id,
			'values' => $field,
			'field_type' => $this->view,
			'roles' => Role::all(true),
			'permissions' => array('R' => array(), 'W' => array()),
		);

		// Load existing permissions
		$read_permissions = Permission::where_name(URLParams::get_type($this->model)."_fields_read_{$field->colname}")->first();
		foreach($read_permissions->roles as $role)
		{
			$data['permissions']['R'][] = $role->id;
		}

		$write_permissions = Permission::where_name(URLParams::get_type($this->model)."_fields_write_{$field->colname}")->first();
		foreach($write_permissions->roles as $role)
		{
			$data['permissions']['W'][] = $role->id;
		}

		$this->layout->nest('content', 'admin.fields.form', $data);
	}

	public function post_add()
	{
		$model = $this->model;
		$name = $this->name;

		if (! $model::is_valid())
		{
			Messages::add('error', $model::$validation->errors->all());
			return Redirect::to($this->views . '/' . $this->view .'/add')->with_input();
		}

		// Add Row
		$field = new $model;
		$field->get_input();

		// By default this is both active and visible.
		$field->active = 1;
		$field->view = 1;

		// If has a type, ensure one gets set
		if(isset($model::$types) && $field->programme_field_type === null)  $field->programme_field_type = 0;
			
		$field->save();

		// Then assign the permissions as specified
		$permissions = Input::get('permissions');

		$permission = Permission::where_name(URLParams::get_type($this->model)."_fields_read_{$field->colname}")->first();
		$permissions['R'] = isset($permissions['R']) ? $permissions['R'] : array();
		$permission->roles()->sync(Role::sanitize_ids($permissions['R']));

		$permission = Permission::where_name(URLParams::get_type($this->model)."_fields_write_{$field->colname}")->first();
		$permissions['W'] = isset($permissions['W']) ? $permissions['W'] : array();
		$permission->roles()->sync(Role::sanitize_ids($permissions['W']));

		Messages::add('success','Row added to schema');

		return Redirect::to(URI::current());
	}

	public function post_edit()
	{
		$model = $this->model;
		$name = $this->name;

		if (! $model::is_valid(null, array('title'  => 'required|max:255', 'id' => 'required', 'type' => 'in:text,textarea,select,checkbox,help,table_select,table_multiselect')))
		{
			Messages::add('error', $model::$validation->errors->all());
			return Redirect::to('/' . $type . '/' . $this->views . '/' . $this->view .'/edit/' . Input::get('id'))->with_input();
		}

		$field = $model::find(Input::get('id'));

		// Grab the old type it used to be before getting input.
		$oldtype = $field->field_type;

		$field->get_input();
	  
		$field->save();

		// Assign permissions
		$permissions = Input::get('permissions');

		$permission = Permission::where_name(URLParams::get_type($this->model)."_fields_read_{$field->colname}")->first();
		$permissions['R'] = isset($permissions['R']) ? $permissions['R'] : array();
		$permission->roles()->sync(Role::sanitize_ids($permissions['R']));

		$permission = Permission::where_name(URLParams::get_type($this->model)."_fields_write_{$field->colname}")->first();
		$permissions['W'] = isset($permissions['W']) ? $permissions['W'] : array();
		$permission->roles()->sync(Role::sanitize_ids($permissions['W']));					
		

		Messages::add('success','Edited field.');

		return Redirect::to(URI::current());
	}


	public function get_deactivate()
	{
		$model = $this->model;
		$row = $model::find(Input::get('id'));
		$row->active = 0;
		$row->save();


		return Redirect::to(dirname(URI::current()));
	}

	public function get_reactivate()
	{
		$model = $this->model;
		$row = $model::find(Input::get('id'));
		$row->active = 1;
		$row->save();

		return Redirect::to(dirname(URI::current()));
	}
	
	/**
	 * Routing for POST /reorder
	 *
	 * This allows fields to be reordered via an AJAX request from the UI
	 */
	public function post_reorder($type)
	{
		$model = $this->model;

		if($model::reorder(Input::get('order'), Input::get('section'))){
			return 'true';
		}else{
			return 'false';
		}
	}

}
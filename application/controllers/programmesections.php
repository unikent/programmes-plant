<?php

use \Verify\Models\Permission;
class ProgrammeSections_Controller extends Admin_Controller {

	public $restful = true;
	public $views = 'sections';
	protected $model = 'ProgrammeSection';
	protected $type = 'ug';

	public function __construct(){

		$this->type = URI::segment(1);
		$this->model = $this->type.'_'.$this->model;

		parent::__construct();
	}

	public function get_index($type)
	{
		return Redirect::to('/'.$type.'/fields/programmes');
	}

	public function get_edit($type, $object_id = false)
	{
		$model = $this->model;
	
		// Do our checks to make sure things are in place
		if(!$object_id) return Redirect::to($this->views);
		
		$object = $model::find($object_id);
		if(!$object) return Redirect::to($this->views);

		$data = array(
			'create' => false,
			'section' => $object,
			'type' => $type,
			'roles' => Role::all(true),
			'permissions' => array('AE' => array()),
		);
		
		// Load existing permissions
		$permissions = Permission::where_name(Mode::get_type().'_sections_autoexpand_' . $object->get_slug())->first();
		foreach($permissions->roles as $role){
			$data['permissions']['AE'][] = $role->id;
		}

		$this->layout->nest('content', 'admin.'.$this->views.'.form', $data);
	}

	/**
	 * Our user subject create function
	 *
	 **/
	public function get_create($type){
		$data = array(
			'create' => true,
			'type' => $type,
			'roles' => Role::all(true),
			'permissions' => array('AE' => array()),
		);

		$this->layout->nest('content', 'admin.'.$this->views.'.form', $data);
	}

	public function post_delete($type){

		$model = $this->model;

		$rules = array(
			'id'  => 'required|exists:programmesections',
		);
		$validation = Validator::make(Input::all(), $rules);
		if ($validation->fails())
		{
			Messages::add('error','You tried to delete a user that doesn\'t exist.');
			return Redirect::to('/'.$type.'/fields/programmes');
		}else{
			$section = $model::find(Input::get('id'));
			$section->delete();
			Messages::add('success','Section Removed');
			return Redirect::to('/'.$type.'/fields/programmes');
		}
	}

	public function post_create($type){

		$model = $this->model;

		$rules = array(
			'name'  => 'required|unique:programmesections|max:255',
		);
		$validation = Validator::make(Input::all(), $rules);
		if ($validation->fails())
		{
			Messages::add('error',$validation->errors->all());
			return Redirect::to('/'.$this->views.'/create')->with_input();
		}else{
			$section = new $model;
			$section->name = Input::get('name');
			$section->save();

			// Now that the section has been saved, create the permission objects
			$permission = new Permission;
			$permission->name = Mode::get_type().'_sections_autoexpand_' . $section->get_slug();
			$permission->save();

			// Then assign the permissions as specified
			$permissions = Input::get('permissions');

			if(isset($permissions['AE']))
			{
				$permission->roles()->sync(Role::sanitize_ids($permissions['AE']));
			}
 
			Messages::add('success','New Section Added');
			return Redirect::to('/'.$type.'/fields/programmes');
		}
	}

	public function post_edit($type){

		$model = $this->model;
		$rules = array(
			'id'  => 'required|exists:programmesections_'.$this->type.',id',
			'name'  => 'required|max:255|unique:programmesections_'.$this->type.',name,'.Input::get('id'),
		);
		
		$validation = Validator::make(Input::all(), $rules);
		
		if ($validation->fails())
		{
			Messages::add('error',$validation->errors->all());
			return Redirect::to('/'.$this->views.'/edit/'.Input::get('id'));
		}
		else
		{
			$section = $model::find(Input::get('id'));
			$section->name = Input::get('name');
			$section->save();

			$permissions = Input::get('permissions');
			if(isset($permissions['AE']))
			{
				$permission = Permission::where_name(Mode::get_type().'_sections_autoexpand_' . $section->get_slug())->first();
				$permission->roles()->sync(Role::sanitize_ids($permissions['AE']));
			}

			Messages::add('success','Section updated');
			return Redirect::to('/'.$type.'/fields/programmes');
		}
	}
	
	/**
	 * Routing for POST /reorder
	 *
	 * This allows fields to be reordered via an AJAX request from the UI
	 */
	public function post_reorder($type)
	{
		$model = $this->model;
		$model::reorder(Input::get('order'));
		die();
	}

}
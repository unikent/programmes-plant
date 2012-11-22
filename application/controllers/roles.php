<?php
class Roles_Controller extends Admin_Controller
{

    public $restful = true;
    public $views = 'roles';

    public function get_index()
    {
    	$this->data[$this->views] = Role::order_by('id','asc')->get();
        return View::make('admin.'.$this->views.'.index',$this->data);
    }

    public function get_edit($object_id = false){
    	// Do our checks to make sure things are in place
    	if(!$object_id) return Redirect::to($this->views);
    	$object = Role::find($object_id);
    	if(!$object) return Redirect::to($this->views);
    	$this->data['role'] = $object;
      
    	return View::make('admin.'.$this->views.'.form',$this->data);
    }

    public function get_create(){
        $this->data['create'] = true;

        return View::make('admin.'.$this->views.'.form',$this->data);
    }

    public function post_delete(){
        $rules = array(
            'id'  => 'required|exists:roles',
        );
        $validation = Validator::make(Input::all(), $rules);
        if ($validation->fails())
        {
            Messages::add('error','You tried to delete a user that doesn\'t exist.');
            return Redirect::to($this->views.'');
        }else{
            $role = Role::find(Input::get('id'));
            $role->delete();
            Messages::add('success','Role Removed');
            return Redirect::to($this->views.'');
        }
    }

    public function post_create(){
        $rules = array(
            'username'  => 'required|unique:roles|max:255',
        );
        $validation = Validator::make(Input::all(), $rules);
        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return Redirect::to($this->views.'/create')->with_input();
        }else{
            $role = new Role;
            $role->username = Input::get('username');
            $role->fullname = Input::get('fullname');
            $role->department = Input::get('department');

            $role->isadmin = Input::get('isadmin') ? true : false;
            $role->isuser = Input::get('isuser') ? true : false;

            $role->save();
 
            Messages::add('success','New Role Added');
            return Redirect::to($this->views.'');
        }
    }

    public function post_edit(){
        $rules = array(
            'id'  => 'required|exists:roles',
            'username'  => 'required|max:255',
        );
        $validation = Validator::make(Input::all(), $rules);
        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return Redirect::to($this->views.'/edit')->with_input();
        }else{
            $role = Role::find(Input::get('id'));
   
            $role->username = Input::get('username');
            $role->fullname = Input::get('fullname');
            $role->department = Input::get('department');

            $role->isadmin = Input::get('isadmin') ? true : false;
            $role->isuser = Input::get('isuser') ? true : false;

            $role->save();

            Messages::add('success','Role updated');
            return Redirect::to($this->views.'');
        }
    }



}
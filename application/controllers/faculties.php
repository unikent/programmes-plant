<?php
class Faculties_Controller extends Admin_Controller
{

    public $restful = true;
    public $views = 'faculties';
    protected $model = 'Faculty';

    public function get_index()
    {
    	$this->data[$this->views] = Faculty::order_by('id','asc')->get();
        return View::make('admin.'.$this->views.'.index',$this->data);
    }

    public function get_edit($year, $type, $object_id = false){
    	// Do our checks to make sure things are in place
    	if(!$object_id) return Redirect::to($this->views);
    	$object = Faculty::find($object_id);
    	if(!$object) return Redirect::to($this->views);
    	$this->data['faculty'] = $object;
      
    	return View::make('admin.'.$this->views.'.form',$this->data);
    }

    /**
     * Our user subject create function
     *
     **/
    public function get_create(){
        $this->data['create'] = true;

        return View::make('admin.'.$this->views.'.form',$this->data);
    }

    public function post_delete(){
        $rules = array(
            'id'  => 'required|exists:faculties',
        );
        $validation = Validator::make(Input::all(), $rules);
        if ($validation->fails())
        {
            Messages::add('error','You tried to delete a user that doesn\'t exist.');
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'');
        }else{
            $faculty = Faculty::find(Input::get('id'));
            $faculty->delete();
            Messages::add('success','Faculty Removed');
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'');
        }
    }

    public function post_create(){
        $rules = array(
            'name'  => 'required|unique:faculties|max:255',
        );
        $validation = Validator::make(Input::all(), $rules);
        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'/create')->with_input();
        }else{
            $faculty = new Faculty;
            $faculty->name = Input::get('name');

            $faculty->save();
 
            Messages::add('success','New Faculty Added');
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'');
        }
    }

    public function post_edit(){
        
        $rules = array(
            'id'  => 'required|exists:faculties,id',
            'name'  => 'required|max:255|unique:faculties,name,'.Input::get('id'),
        );
        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'/edit/'.Input::get('id'));
        }else{
            $faculty = Faculty::find(Input::get('id'));
   
            $faculty->name = Input::get('name');

            $faculty->save();

            Messages::add('success','Faculty updated');
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'');
        }
    }



}
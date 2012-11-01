<?php
class Schools_Controller extends Admin_Controller
{

    public $restful = true;
    public $views = 'schools';
    protected $model = 'School';

    public function get_index()
    {
    	$this->data[$this->views] = School::order_by('id','asc')->get();
        return View::make('admin.'.$this->views.'.index',$this->data);
    }

    public function get_edit($year, $type, $object_id = false){
    	// Do our checks to make sure things are in place
    	if(!$object_id) return Redirect::to($this->views);
    	$object = School::find($object_id);
    	if(!$object) return Redirect::to($this->views);
    	$this->data['school'] = $object;
      
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
            'id'  => 'required|exists:schools',
        );
        $validation = Validator::make(Input::all(), $rules);
        if ($validation->fails())
        {
            Messages::add('error','You tried to delete a user that doesn\'t exist.');
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'');
        }else{
            $school = School::find(Input::get('id'));
            $school->delete();
            Messages::add('success','School Removed');
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'');
        }
    }

    public function post_create(){
        $rules = array(
            'name'  => 'required|unique:schools|max:255',
            'faculty'  => 'required|exists:faculties,id'
        );
        
        $validation = Validator::make(Input::all(), $rules);
        
        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'/create')->with_input();
        }else{
            $school = new School;
            $school->name = Input::get('name');
            $school->faculties_id = Input::get('faculty');

            $school->save();
 
            Messages::add('success','New School Added');
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'');
        }
    }

    public function post_edit(){
        
        $rules = array(
            'id'  => 'required|exists:schools',
            'name'  => 'required|max:255|unique:schools,name,'.Input::get('id'),
            'faculty'  => 'required|exists:faculties,id'
        );
        
        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'/edit/'.Input::get('id'));
        }else{
            $school = School::find(Input::get('id'));
   
            $school->name = Input::get('name');
            $school->faculties_id = Input::get('faculty');

            $school->save();

            Messages::add('success','School updated');
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'');
        }
    }



}
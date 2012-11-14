<?php
class Schools_Controller extends Simple_Admin_Controller
{

    public $restful = true;
    public $views = 'schools';
    protected $model = 'School';

    public function get_edit($year, $type, $object_id = false)
    {
    	// Do our checks to make sure things are in place
    	if(!$object_id) return Redirect::to($this->views);

    	$object = School::find($object_id);

    	if(!$object) return Redirect::to($this->views);

    	$this->data['school'] = $object;
      
    	return View::make('admin.'.$this->views.'.form',$this->data);
    }

    public function get_create()
    {
        $this->data['create'] = true;

        return View::make('admin.'.$this->views.'.form',$this->data);
    }

    public function post_create()
    {
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

    public function post_edit()
    {
        
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
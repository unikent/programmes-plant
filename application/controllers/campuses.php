<?php
class Campuses_Controller extends Admin_Controller
{

    public $restful = true;
    public $views = 'campuses';
    protected $model = 'Campus';

    public function get_index()
    {
    	$this->data[$this->views] = Campus::order_by('id','asc')->get();
        return View::make('admin.'.$this->views.'.index',$this->data);
    }

    public function get_edit($year, $type, $object_id = false){
    	// Do our checks to make sure things are in place
    	if(!$object_id) return Redirect::to($this->views);
    	$object = Campus::find($object_id);
    	if(!$object) return Redirect::to($this->views);
    	$this->data['campus'] = $object;
      
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
            'id'  => 'required|exists:campuses',
        );
        $validation = Validator::make(Input::all(), $rules);
        if ($validation->fails())
        {
            Messages::add('error','You tried to delete a user that doesn\'t exist.');
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'');
        }else{
            $campus = Campus::find(Input::get('id'));
            $campus->delete();
            Messages::add('success','Campus Removed');
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'');
        }
    }

    public function post_create(){
        $rules = array(
            'name'  => 'required|unique:campuses|max:255',
        );
        $validation = Validator::make(Input::all(), $rules);
        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'/create')->with_input();
        }else{
            $campus = new Campus;
            $campus->name = Input::get('name');

            $campus->save();
 
            Messages::add('success','New Campus Added');
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'');
        }
    }

    public function post_edit(){
        
        $rules = array(
            'id'  => 'required|exists:campuses,id',
            'name'  => 'required|max:255|unique:campuses,name,'.Input::get('id'),
        );
        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'/edit/'.Input::get('id'));
        }else{
            $campus = Campus::find(Input::get('id'));
   
            $campus->name = Input::get('name');

            $campus->save();

            Messages::add('success','Campus updated');
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'');
        }
    }



}
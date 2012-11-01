<?php
class Awards_Controller extends Admin_Controller
{

    public $restful = true;
    public $views = 'awards';
    protected $model = 'Award';

    public function get_index()
    {
    	$this->data[$this->views] = Award::order_by('id','asc')->get();
        return View::make('admin.'.$this->views.'.index',$this->data);
    }

    public function get_edit($year, $type, $object_id = false){
    	// Do our checks to make sure things are in place
    	if(!$object_id) return Redirect::to($this->views);
    	$object = Award::find($object_id);
    	if(!$object) return Redirect::to($this->views);
    	$this->data['award'] = $object;
      
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
            'id'  => 'required|exists:awards',
        );
        $validation = Validator::make(Input::all(), $rules);
        if ($validation->fails())
        {
            Messages::add('error','You tried to delete a user that doesn\'t exist.');
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'');
        }else{
            $award = Award::find(Input::get('id'));
            $award->delete();
            Messages::add('success','Award Removed');
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'');
        }
    }

    public function post_create(){
        $rules = array(
            'name'  => 'required|unique:awards|max:255',
        );
        $validation = Validator::make(Input::all(), $rules);
        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'/create')->with_input();
        }else{
            $award = new Award;
            $award->name = Input::get('name');

            $award->save();
 
            Messages::add('success','New Award Added');
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'');
        }
    }

    public function post_edit(){
        
        $rules = array(
            'id'  => 'required|exists:awards,id',
            'name'  => 'required|max:255|unique:awards,name,'.Input::get('id'),
        );
        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'/edit/'.Input::get('id'));
        }else{
            $award = Award::find(Input::get('id'));
   
            $award->name = Input::get('name');

            $award->save();

            Messages::add('success','Award updated');
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'');
        }
    }



}
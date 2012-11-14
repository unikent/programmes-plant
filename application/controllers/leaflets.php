<?php
class Leaflets_Controller extends Simple_Admin_Controller
{

    public $restful = true;
    public $views = 'leaflets';
    protected $model = 'Leaflet';

    public function get_edit($year, $type, $object_id = false)
    {
    	// Do our checks to make sure things are in place
    	if(!$object_id) return Redirect::to($this->views);

    	$object = Leaflet::find($object_id);

    	if(!$object) return Redirect::to($this->views);

    	$this->data['leaflet'] = $object;
      
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
            'name'  => 'required|unique:leaflets|max:255',
            'campus'  => 'required|exists:campuses,id',
            'tracking_code'  => 'required|url'
        );
        
        $validation = Validator::make(Input::all(), $rules);
        
        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'/create')->with_input();
        }else{
            $leaflet = new Leaflet;
            $leaflet->name = Input::get('name');
            $leaflet->campuses_id = Input::get('campus');
            $leaflet->tracking_code = Input::get('tracking_code');

            $leaflet->save();
 
            Messages::add('success','New Leaflet Added');
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'');
        }
    }

    public function post_edit()
    {
        
        $rules = array(
            'id'  => 'required|exists:leaflets',
            'name'  => 'required|max:255|unique:leaflets,name,'.Input::get('id'),
            'campus'  => 'required|exists:campuses,id',
            'tracking_code'  => 'required|url'
        );
        
        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'/edit/'.Input::get('id'));
        }else{
            $leaflet = Leaflet::find(Input::get('id'));
   
            $leaflet->name = Input::get('name');
            $leaflet->campuses_id = Input::get('campus');
            $leaflet->tracking_code = Input::get('tracking_code');

            $leaflet->save();

            Messages::add('success','Leaflet updated');
            return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'');
        }
    }

}
<?php
class Leaflets_Controller extends Simple_Admin_Controller
{

    public $restful = true;
    public $views = 'leaflets';
    protected $model = 'Leaflet';
    public $custom_form = true;

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
        }
        else
        {
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
        }
        else
        {
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
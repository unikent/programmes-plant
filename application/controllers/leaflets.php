<?php
class Leaflets_Controller extends Simple_Admin_Controller
{

    public $restful = true;
    public $views = 'leaflets';
    protected $model = 'Leaflet';
    public $custom_form = true;

    public function post_create()
    {
        $model = $this->model;

        if (! $model::is_valid())
        {
            Messages::add('error', $model::$validation->errors->all());

            return Redirect::to(URI::segment(1) . '/' . URI::segment(2) . '/' . $this->views . '/create')->with_input();
        }
        
        $leaflet = new Leaflet;
        $leaflet->input();
        $leaflet->save();

        Messages::add('success', 'New Leaflet Added');
        return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'');
    }

    public function post_edit()
    {
        $model = $this->model;
        
        $rules = array(
            'id'  => 'required|exists:leaflets'
        );
        
        if (! $model::is_valid($rules))
        {
            Messages::add('error', $model::$validation->errors->all());
            return Redirect::to(URI::segment(1) . '/' . URI::segment(2) . '/' . $this->views . '/edit/' . Input::get('id'));
        }

        $leaflet = Leaflet::find(Input::get('id'));
        $leaflet->input();
        $leaflet->save();

        Messages::add('success', 'Leaflet updated');
        return Redirect::to(URI::segment(1).'/'.URI::segment(2).'/'.$this->views.'');
    }

}
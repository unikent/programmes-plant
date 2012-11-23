<?php
class Leaflet extends SimpleData
{
	public static $rules = array(
            'name'  => 'required|unique:leaflets|max:255',
            'campus'  => 'required|exists:campuses,id',
            'tracking_code'  => 'required|url'
    );

    public function input()
    {
    	$this->name = Input::get('name');
        $this->campuses_id = Input::get('campus');
        $this->tracking_code = Input::get('tracking_code');
    }
    
}
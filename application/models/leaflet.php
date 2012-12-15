<?php
class Leaflet extends SimpleData
{
	public static $rules = array(
            'campus'  => 'required|exists:campuses,id',
            'tracking_code'  => 'required|url'
    );
    
}
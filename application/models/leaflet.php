<?php
class Leaflet extends SimpleData
{
	public static $rules = array(
			'name'  => 'required|unique:leaflets|max:255',
            'campus'  => 'required|exists:campuses,id',
            'tracking_code'  => 'required|url'
    );
}
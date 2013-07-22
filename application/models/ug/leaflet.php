<?php
class UG_Leaflet extends SimpleData
{
	public static $table = 'leaflets_ug';
	public static $rules = array(
			'name'  => 'required|unique:leaflets_ug|max:255',
            'campus'  => 'required|exists:campuses,id',
            'tracking_code'  => 'required|url'
    );
}
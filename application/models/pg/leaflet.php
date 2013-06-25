<?php
class PG_Leaflet extends SimpleData
{
	public static $table = 'leaflets_pg';
	public static $rules = array(
			'name'  => 'required|unique:leaflets|max:255',
            'campus'  => 'required|exists:campuses,id',
            'tracking_code'  => 'required|url'
    );
}
<?php
class Campus extends SimpleData
{
	public static $rules = array(
    	'name' => 'required|unique:leaflets|max:255',
    );
}
<?php
class Campus extends SimpleData
{
	public static $rules = array(
    	'name' => 'required|unique:campuses|max:255',
    	'identifier' => 'numeric',
    	'address_1' => 'required',
    	'address_2' => 'required',
    	'address_3' => 'required',
    	'email' => 'email',
    	'phone' => 'required|match:/^([0-9 ]+)$/',
    	'postcode' => 'required',
    	'url' => 'url'
    );
}
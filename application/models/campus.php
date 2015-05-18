<?php
class Campus extends SimpleData {

	public static $table = 'campuses';

	public static $rules = array(
		'name' => 'required|unique:campuses|max:255',
		'identifier' => 'numeric',
		'address_1' => 'string',
		'address_2' => 'string',
		'email' => 'email',
		'phone' => 'match:/^([0-9 \-+\(\)])/',
		'postcode' => 'string',
		'url' => 'url'
	);
	
}
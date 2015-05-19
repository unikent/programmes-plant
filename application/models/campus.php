<?php
class Campus extends SimpleData {

	public static $table = 'campuses';

	public static $rules = array(
		'name' => 'required|unique:campuses|max:255',
		'identifier' => 'numeric',
		'address_1' => 'max:255',
		'address_2' => 'max:255',
		'email' => 'email',
		'phone' => 'match:/^([0-9 \-+\(\)])/',
		'postcode' => 'max:255',
		'url' => 'url'
	);

}

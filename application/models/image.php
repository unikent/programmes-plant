<?php
class Image extends SimpleData
{
	public static $table = 'images';

	public static $rules = array(
		'name'  => 'required'
		'file_name' => 'requried'
	);
	
}
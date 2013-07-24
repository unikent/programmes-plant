<?php
class School extends SimpleData
{
	public static $table = 'schools';

	public static $rules = array(
		'faculty'  => 'required|exists:faculties,id'
	);

	public function input()
	{
		$this->name = Input::get('name');
        $this->faculties_id = Input::get('faculty');
	}
	
}
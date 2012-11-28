<?php
class School extends SimpleData
{

	public static $rules = array(
		'name'  => 'required|unique:schools|max:255',
		'faculty'  => 'required|exists:faculties,id'
	);

	public function input()
	{
		$this->name = Input::get('name');
        $this->faculties_id = Input::get('faculty');
	}
	
}
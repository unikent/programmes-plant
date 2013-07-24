<?php
class Faculty extends SimpleData
{

	public static $table = 'faculties';
	
    public function comments()
	{
		return $this->has_many('School');
	}

}
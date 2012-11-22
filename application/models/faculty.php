<?php
class Faculty extends SimpleData
{

    public function comments()
	{
		return $this->has_many('School');
	}

}
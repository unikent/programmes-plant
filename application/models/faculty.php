<?php
class Faculty extends Eloquent {
	
	public static function getAsList(){
       $data = Faculty::get(); $options = array();
       foreach ($data as $record) $options[$record->id] = $record->name;

       return $options;
    }

    public function comments()
	{
		return $this->has_many('School');
	}
}
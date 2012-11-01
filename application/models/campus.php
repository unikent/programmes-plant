<?php
class Campus extends Eloquent {

	public static function getAsList(){
       $data = Campus::get(); $options = array();
       foreach ($data as $record) $options[$record->id] = $record->name;

       return $options;
     }
}
<?php
class School extends Eloquent {

	public static function getAsList(){
       $data = School::get(); $options = array();
       foreach ($data as $record) $options[$record->id] = $record->name;

       return $options;
     }
}
<?php
class Award extends Eloquent {

	public static function getAsList(){
       $data = Award::get(); $options = array();
       foreach ($data as $record) $options[$record->id] = $record->name;

       return $options;
     }
}
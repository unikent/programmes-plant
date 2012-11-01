<?php
class Leaflet extends Eloquent {

	public static function getAsList(){
       $data = Leaflet::get(); $options = array();
       foreach ($data as $record) $options[$record->id] = $record->name;

       return $options;
     }
}
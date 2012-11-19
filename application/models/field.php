<?php

class Field extends Eloquent {
    
    /**
    * update the order value for a given class of fields
    *
    * this is called from an ajax action in the controller, in turn from reordering fields in the ui
    *
    * @param string $order_string a comma-separated list of fields, in the order in which the user wants them
    */
    public static function reorder($order_string)
    {
        // break up the string to get the list of ids
        $order_array = explode(",", $order_string);
        
        // loop through the array of ids and update each one in the db
        foreach ($order_array as $counter => $id)
        {
            // strip out the non-relevant part of the html id to get the actual id
            $id = str_replace('field-ordering-id-', '', $id);
            
            // pull out the appropriate entry and update it with the array index (+1)
            $item = self::find($id);
            $item->order = $counter + 1;
            $item->save(); 
        }
    }
    
}
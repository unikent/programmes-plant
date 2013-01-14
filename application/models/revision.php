<?php
/**
* Base model for "revision" instances.
*
*/
class Revision extends Eloquent {

	//Id used to link items of datatype (optional)
	protected $data_type_id = false;

	/**
	 * Get identifier (string representing this revision)
	 * 
	 * @return string identifer 
	 */
	public function get_identifier(){
        return "R".$this->{$this->data_type_id}."-{$this->id}";
    }

    /**
	 * Get display string for revision
	 * 
	 * @return string decribing revision
	 */
    public function get_identifier_string(){
        return '<strong>'.$this->get_identifier().'</strong> created '.$this->get_created_time().' by '.$this->edits_by ;
    }

    /**
	 * Get formatted published time
	 * 
	 * @return string Published date
	 */
    public function get_published_time(){
        return Date("jS F Y \a\\t H:i:s" ,strtotime($this->published_at));
    }

     /**
	 * Get formatted creation time
	 * 
	 * @return string creation date
	 */
    public function get_created_time(){
        return Date("jS F Y \a\\t H:i:s", strtotime($this->created_at));
    }
}
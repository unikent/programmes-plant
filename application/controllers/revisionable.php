<?php

class Revisionable_Controller extends Admin_Controller
{

	
	private function get_revision_data($revisionable_item_id, $revision_id){

		$model = $this->model;

        //Ensure item & revision id are supplied
        if(!$revisionable_item_id || !$revision_id) return false;

		//Ensure object exists
        $revisionable_item = $model::find($revisionable_item_id);
        if (!$revisionable_item) return false;

        //Ensure Revision exists
        $revision = $revisionable_item->find_revision($revision_id);
        if (!$revision) return false;

        return array($revisionable_item,$revision);

    }


    /**
     * Routing for GET /$year/$type/programmes/$programme_id/revert_to_revision/$revision_id
     */
    public function get_revert_to_revision($year, $type, $revisionable_item_id = false, $revision_id = false){
        
        // Check to see we have what is required.
    	$data = $this->get_revision_data($revisionable_item_id, $revision_id);
    	//If somthing went wrong
    	if(!$data) return Redirect::to($year.'/'.$type.'/'.$this->views);

    	//Get data & revert to revision
    	list($item, $revision) = $data;
        $item->revertToRevision($revision);

        //Redirect to point of origin
        Messages::add('success', "Reverted to previous revision.");
        return Redirect::to($year.'/'.$type.'/'.$this->views.'/edit/'.$item->id);
    }


    /**
     * Routing for GET /$year/$type/programmes/$programme_id/make_live/$revision_id
     */
	public function get_make_live($year, $type, $revisionable_item_id = false, $revision_id = false){

        // Check to see we have what is required.
    	$data = $this->get_revision_data($revisionable_item_id, $revision_id);
    	//If somthing went wrong
    	if(!$data) return Redirect::to($year.'/'.$type.'/'.$this->views);

    	//Get data & make revision live
    	list($item, $revision) = $data;
        $item->makeRevisionLive($revision);
        
        //@todo
        //physical make it live, ping aggrigator or somthing

        //Redirect to point of origin
        Messages::add('success', "The selected revision has been made live.");
        return Redirect::to($year.'/'.$type.'/'.$this->views.'/edit/'.$item->id);
    }
    




}
<?php

class Revisionable_Controller extends Admin_Controller {

	/**
	 * Ensure valid revisionable_item & revision have been provided. Return false if not.
	 *
	 * @param int    $revisionable_item_id  ID of the object with revisions.
	 * @param int    $revision_id The id of the specific revision an action is being taken on
	 *
	 * @return false | array(revisonble Object, revision object)
	 */
	private function get_revision_data($revisionable_item_id, $revision_id)
	{
		//Handle global settings & programesettings not having a revisionable_item_id (is always 1)
		if($this->views =='globalsettings' || $this->views =='programmesettings'){
			$revision_id = $revisionable_item_id;
			$revisionable_item_id = 1;
		}

		//Get model
		$model = $this->model;

		//Ensure item & revision id are supplied
		if(!$revisionable_item_id || !$revision_id) return false;

		//Ensure object exists
		$revisionable_item = $model::find($revisionable_item_id);
		if (!$revisionable_item) return false;

		//Ensure Revision exists
		$revision = $revisionable_item->get_revision($revision_id);
		if (!$revision) return false;
		return array($revisionable_item,$revision);

	}

	/**
	 * Routing for GET /$year/$type/$object_id/revert_to_revision/$revision_id
	 * Routing for GET /$year/$type/revert_to_revision/$revision_id
	 *
	 * @param int    $year         The year of the programme (not used, but to keep routing happy).
	 * @param string $type         The type, either undegrad/postgrade (not used, but to keep routing happy)
	 * @param int    $revisionable_item_id  The object ID we are reverting to a revision on.
	 * @param int    $revision_id  The revision ID we are reverting to.
	 *
	 * @note revisionable_item_id id can be ommited, resulting in that varible containg 
	 *	the revision id instead. (Object assumed to have id 1 in this case)
	 */
	public function get_revert_to_revision($year, $type, $revisionable_item_id = false, $revision_id = false)
	{
		
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
	 * Routing for GET /$year/$type/$object_id/make_live/$revision_id
	 * Routing for GET /$year/$type/make_live/$revision_id
	 *
	 * @param int    $year         The year of the programme (not used, but to keep routing happy).
	 * @param string $type         The type, either undegrad/postgrade (not used, but to keep routing happy)
	 * @param int    $revisionable_item_id  The object ID we are pushing a revision live on.
	 * @param int    $revision_id  The revision ID we are putting live.
	 *
	 * @note revisionable_item_id id can be ommited, resulting in that varible containg 
	 *	the revision id instead. (Object assumed to have id 1 in this case)
	 */
	public function get_make_live($year, $type, $revisionable_item_id = false, $revision_id = false)
	{

		// Check to see we have what is required.
		$data = $this->get_revision_data($revisionable_item_id, $revision_id);
		//If somthing went wrong
		if(!$data) return Redirect::to($year.'/'.$type.'/'.$this->views);

		//Get data & make revision live
		list($item, $revision) = $data;
		$modified_revision = $item->make_revision_live($revision);
		


		//Redirect to point of origin
		Messages::add('success', "The selected revision has been made live.");
		return Redirect::to($year.'/'.$type.'/'.$this->views.'/edit/'.$item->id);
	}



	public function get_revisions($year, $type, $itm_id = false){

		$model = $this->model;
		$course = $model::find($itm_id);
		if(!$course) return Redirect::to($year.'/'.$type.'/'.$this->views);

		$this->data['programme'] = $course ;
		if ($revisions = $course->get_revisions()) {
				$this->data['revisions'] =  $revisions;
		}




		$this->layout->nest('content', 'admin.revisions.index', $this->data);
	}

}
<?php

use Laravel\Redirect;

class Editor_Controller extends Admin_Controller {
	
	public $restful = true;

	public $required_permissions = array('recieve_edit_requests');

	public function __construct()
	{  
		// Construct parent.
		parent::__construct();
	}

	public function get_inbox()
	{	
		$ug_for_review = UG_Programme::get_under_review();
		$pg_for_review = PG_Programme::get_under_review();

		//merge undergraduate and postgraduate together
		$for_review = array_merge($ug_for_review, $pg_for_review);

		//sort them by last updated date
		usort($for_review, function($a, $b){
			 return Date::forge($a->updated_at) > Date::forge($b->updated_at) ? -1 : 1;
		});

		return $this->layout->nest('content', 'admin.editor.index', array('for_review' => $for_review));
	}

	public function get_remove($type,$id){

		if(in_array($type,array('pg','ug'))){
			$model = strtoupper($type) . '_ProgrammeRevision';
			$revision = $model::find($id);
			$revision->under_review = 0;
			$revision->save();
		}
		return Redirect::to_action('editor@inbox');
	}

}
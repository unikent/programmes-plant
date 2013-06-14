<?php
class Dash_Controller extends Admin_Controller {
	
	public $restful = true;

	public function get_index()
	{
		$this->data['programmes'] =  UG_Programme::where('year','=',URI::segment(1))->get();

		return View::make('admin.index', $this->data);
	}

}
<?php

class Settings_Controller extends Admin_Controller {

	public $restful = true;

	public function __construct(){

	}
	
	public function get_index()
	{
		$data = Settings::find(1);

		$this->layout->nest('content', 'admin.settings.edit', $data);
	}
	public function post_index()
	{

		$data = Settings::find(1);

		$this->layout->nest('content', 'admin.settings.edit', $data);
	}


}
<?php

class Settings_Controller extends Admin_Controller {

	public $restful = true;
	public $required_permissions = array("system_settings");


	public function get_index()
	{
		$data['settings'] = Setting::find(1);

		$this->layout->nest('content', 'admin.settings.edit', $data);
	}

	public function post_index()
	{

		if (! Setting::is_valid())
		{
			Messages::add('error', Setting::$validation->errors->all());
			Input::flash();//Save previous inputs to avoid blanking form.
			return Redirect::to('settings');
		}

		$update = Setting::find(1);
		$update->populate_from_input();
		$update->raw_save();

		Messages::add('success',"System settings updated");

		return Redirect::to('settings');
	}


}
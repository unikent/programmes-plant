<?php

class Subjects_Controller extends Simple_Admin_Controller {

	public $restful = true;
	public $views = 'subjects';
	public $model = 'Subject';
	public $custom_form = true;

	public $shared_data = false;
}
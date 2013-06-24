<?php
class Awards_Controller extends Simple_Admin_Controller
{
	public $restful = true;
	public $views = 'awards';
	protected $model = 'Award';
	public $custom_form = true;

	public $shared_data = false;
}
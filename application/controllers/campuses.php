<?php
class Campuses_Controller extends Simple_Admin_Controller {

	public $restful = true;
	public $views = 'campuses';
	public $model = 'Campus';
	public $custom_form = true;

	public function post_edit()
	{
		Cache::purge('api-index-pg');
		Cache::purge('api-index-ug');
		API::purge_output_cache();
		return parent::post_edit();
	}
}
<?php
class Leaflets_Controller extends Simple_Admin_Controller {

	public $restful = true;
	public $views = 'leaflets';
	protected $model = 'Leaflet';
	public $custom_form = true;

}
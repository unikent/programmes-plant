<?php

class GlobalSettings_Controller extends Revisionable_Controller {

	public $restful = true;
	public $views = 'globalsettings';
	protected $model = 'GlobalSetting';

	/**
	 * Routing for /$year/$type/globalsettings
	 *
	 * @param int    $year The year.
	 * @param string $type Undergraduate or postgraduate.
	 */
	public function get_index($year, $type)
	{

		$model = $this->model;
		$data = $model::where('year', '=', $year)->first();
		if ($data == null) {
			return Redirect::to($year.'/'.$type.'/'.$this->views.'/create');
		} else {
			return Redirect::to($year.'/'.$type.'/'.$this->views.'/edit');
		}

	}

	/**
	 * Our global setting create function
	 */
	public function get_create($year, $type)
	{

		$this->data['fields'] = $this->get_fields();
		
		$this->data['create'] = true;

		$this->layout->nest('content', 'admin.'.$this->views.'.form', $this->data);
	}

	/**
	 * Routing for GET /$year/$type/edit/$subject_id
	 *
	 * @param int    $year       The year of the subject
	 * @param string $type       The type of the subject undergraduate/postgraduate
	 * @param int    $subject_id The ID of the subject to edit.
	 */
	public function get_edit($year, $type)
	{
		$model = $this->model;
		$globalsetting = $model::where('year', '=', $year)->first();

		if(!$globalsetting) return Redirect::to($year.'/'.$type.'/'.$this->views);

		$this->data[$this->views] = $globalsetting ;

		$this->data['active_revision'] = $globalsetting->get_active_revision();

		$this->data['fields'] = $this->get_fields();

		$this->layout->nest('content', 'admin.'.$this->views.'.form', $this->data);
	}

	/**
	 * Routing for POST /$year/$type/create
	 *
	 * The change request page.
	 *
	 * @param int    $year The year of the created settings data
	 * @param string $type The type, either ug (undergraduate) or pg (postgraduate)
	 */
	public function post_create($year, $type)
	{

			$global_settings = new GlobalSetting;
			$global_settings->year = Input::get('year');

			 //Save varible fields
			$f = $this->get_fields();
			foreach ($f as $c) {
				$col = $c->colname;
				if(Input::get($col) != null)  $global_settings->$col = Input::get($col);
			}

			$global_settings->save();

			Messages::add('success','Global settings have been saved');

			return Redirect::to($year.'/'.$type.'/'.$this->views.'');

	}

	/**
	 * Routing for POST /$year/$type/edit
	 *
	 * Make a change.
	 *
	 * @param int    $year The year of the settings data
	 * @param string $type The type, either ug (undergraduate) or pg (postgraduate)
	 */
	public function post_edit($year, $type)
	{

			$global_settings = GlobalSetting::where('year', '=', $year)->first();

			$global_settings->year = Input::get('year');

			//Save varible fields
			$f = $this->get_fields();
			foreach ($f as $c) {
				$col = $c->colname;
				if(Input::get($col) != null)  $global_settings->$col = Input::get($col);
			}

			$global_settings->save();

			$institution_name_field = GlobalSetting::get_institution_name_field();

			Messages::add('success', "Saved {$global_settings->$institution_name_field}.");

			return Redirect::to($year.'/'. $type.'/'. $this->views);

	}

	private function get_fields()
	{
		$model = 'GlobalSettingField';

		return  $model::where('active','=','1')->order_by('field_name','asc')->get();
	}

	/**
	 * Routing for GET /$year/$type/subjects/$subject_id/promote/$revision_id
	 *
	 * @param int    $year        The year of the subject (not used, but to keep routing happy).
	 * @param string $type        The type, either undegrad/postgrade (not used, but to keep routing happy)
	 * @param int    $subject_id  The subject ID we are promoting a given revision to be live.
	 * @param int    $revision_id The revision ID we are promote to the being the live output for the subject.
	 */
	public function get_promote($year, $type, $revision_id = false)
	{
		// Get revision specified
		$globalsetting = GlobalSetting::where('year', '=', $year)->first();

		if (!$globalsetting) return Redirect::to($year.'/'.$type.'/'.$this->views);

		$revision = $globalsetting->find_revision($revision_id);

		if (!$revision) return Redirect::to($year.'/'.$type.'/'.$this->views);

		$globalsetting->useRevision($revision);
		
		Messages::add('success', "Promoted revision created at $revision->updated_at to live version.");

		return Redirect::to($year.'/'.$type.'/'.$this->views.'');
	}

	/**
	 * Routing for GET /$year/$type/subjects/$subject_id/difference/$revision_id
	 *
	 * @param int    $year        The year of the subject (not used, but to keep routing happy).
	 * @param string $type        The type, either undegrad/postgrade (not used, but to keep routing happy)
	 * @param int    $subject_id  The subject ID we are promoting a given revision to be live.
	 * @param int    $revision_id The revision ID we are promote to the being the live output for the subject.
	 */
	public function get_difference($year, $type, $revision_id = false)
	{
		// Get revision specified
		$globalsetting = GlobalSetting::where('year', '=', $year)->first();

		if (!$globalsetting) return Redirect::to($year.'/'.$type.'/'.$this->views);

		$revision = $globalsetting->find_revision($revision_id);
		
		if (!$revision) return Redirect::to($year.'/'.$type.'/'.$this->views);

		$revision_attributes = $globalsetting->attributes;
		$revision_for_diff = (array) $revision;

		// Ignore these fields which will always change
		foreach (array('id', 'created_by', 'published_by', 'created_at', 'updated_at', 'live') as $ignore) {
			unset($revision_for_diff[$ignore]);
			unset($revision_attributes[$ignore]);
		}

		$differences = array_diff_assoc($revision_attributes, $revision_for_diff);

		$diff = array();

		foreach ($differences as $field => $value) {
			$diff[$field] = SimpleDiff::htmlDiff($revision_attributes[$field], $revision_for_diff[$field]);
		}

		$this->data['diff'] = $diff;
		$this->data['new'] = $revision_for_diff;
		$this->data['old'] = $revision_attributes;
		$this->data['attributes'] = GlobalSetting::getAttributesList();

		$this->data['revision'] = $revision;
		$this->data['globalsetting'] = $globalsetting;

		return View::make('admin.'.$this->views.'.difference',$this->data);
	}

	/**
	 * Routing for GET /changes
	 *
	 * The change request page.
	 */
	public function get_changes()
	{
	   $this->data['revisions'] = DB::table('global_settings_revisions')
			->where('status', '=', 'pending')
			->get();

		return View::make('admin.changes.index', $this->data);
	}
}

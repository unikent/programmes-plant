<?php

class GlobalSettings_Controller extends Revisionable_Controller {

	public $restful = true;
	public $views = 'globalsettings';
	protected $model = 'GlobalSetting';

	public $required_permissions = array("edit_immutable_data");
	/**
	 * Routing for /$year/globalsettings
	 *
	 * @param int    $year The year.
	 */
	public function get_index($year)
	{

		$model = $this->model;
		$data = $model::where('year', '=', $year)->first();
		if ($data == null) {
			return Redirect::to($year.'/'.$this->views.'/create');
		} else {
			return Redirect::to($year.'/'.$this->views.'/edit');
		}

	}

	/**
	 * Our global setting create function
	 */
	public function get_create($year)
	{

		$this->data['fields'] = $this->get_fields();
		
		$this->data['create'] = true;
		$this->data['model'] = $this->model;

		$this->layout->nest('content', 'admin.'.$this->views.'.form', $this->data);
	}

	/**
	 * Routing for GET /$year/edit/$subject_id
	 *
	 * @param int    $year       The year of the subject
	 * @param int    $subject_id The ID of the subject to edit.
	 */
	public function get_edit($year)
	{
		$model = $this->model;
		$globalsetting = $model::where('year', '=', $year)->first();

		if(!$globalsetting) return Redirect::to($year.'/'.$this->views);

		$this->data[$this->views] = $globalsetting ;

		$this->data['active_revision'] = $globalsetting->get_active_revision();

		$this->data['fields'] = $this->get_fields();
		$this->data['model'] = $model;
		

		$this->layout->nest('content', 'admin.'.$this->views.'.form', $this->data);
	}

	/**
	 * Routing for POST /$year/create
	 *
	 * The change request page.
	 *
	 * @param int    $year The year of the created settings data
	 */
	public function post_create($year)
	{

			$global_settings = new GlobalSetting;
			$global_settings->year = Input::get('year');

			 //Save varible fields
			$f = $this->get_fields();
			foreach ($f as $c) {
				$col = $c->colname;
				$global_settings->$col = Input::get($col);
			}

			$global_settings->save();

			Messages::add('success','Global settings have been saved');

			return Redirect::to($year.'/'.$this->views.'/edit');

	}

	/**
	 * Routing for POST /$year/edit
	 *
	 * Make a change.
	 *
	 * @param int    $year The year of the settings data
	 */
	public function post_edit($year)
	{

			$global_settings = GlobalSetting::where('year', '=', $year)->first();

			$global_settings->year = Input::get('year');

			//Save varible fields
			$f = $this->get_fields();
			foreach ($f as $c) {
				$col = $c->colname;
				$global_settings->$col = Input::get($col);
			}

			$global_settings->save();

			$institution_name_field = GlobalSetting::get_institution_name_field();

			Messages::add('success', "Saved {$global_settings->$institution_name_field}.");

			return Redirect::to($year.'/'. $this->views.'/edit');

	}

	private function get_fields()
	{
		$model = 'GlobalSettingField';

		return  $model::where('active','=','1')->order_by('field_name','asc')->get();
	}


	/**
	 * Routing for GET /$year/subjects/$subject_id/difference/$revision_id
	 *
	 * @param int    $year        The year of the subject (not used, but to keep routing happy).
	 * @param int    $subject_id  The subject ID we are promoting a given revision to be live.
	 * @param int    $revision_id The revision ID we are promote to the being the live output for the subject.
	 */
	public function get_difference($year, $revision_id = false)
	{
		// Get revision specified
		$globalsetting = GlobalSetting::where('year', '=', $year)->first();

		if (!$globalsetting) return Redirect::to($year.'/'.$this->views);

		$revision = $globalsetting->find_revision($revision_id);
		
		if (!$revision) return Redirect::to($year.'/'.$this->views);

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

	/**
	 * Routing for GET /$year/delete/$programme_id
	 *
	 * @param int    $year         The year of the programme (not used, but to keep routing happy).
	 * @param int    $revisionable_item_id  The object ID we are pushing a revision live on.
	 * @param int    $misc         Only here to satisfy rules of overriding parent methods, so it isn't used.
	 *
	 * @note revisionable_item_id id can be ommited, resulting in that varible containg 
	 *	the revision id instead. (Object assumed to have id 1 in this case)
	 */
	public function get_delete($year, $revisionable_item_id = false, $misc = false)
	{
		return parent::get_delete($year, URLParams::get_type(), $revisionable_item_id);
	}

	/**
	 * Routing for GET /$year/globalsettings/revisions/{$itm_id}
	 *
	 * @param int    $year         The year of the programme (not used, but to keep routing happy).
	 * @param string $itm_id       The id of the item to show revisions for
	 * @param int    $misc         Only here to satisfy rules of overriding parent methods, so it isn't used.
	*/
	public function get_revisions($year, $itm_id = false, $misc = false)
	{
		return parent::get_revisions($year, URLParams::get_type(), $itm_id);
	}

	/**
	 * Routing for GET /$year/$object_id/make_live/$revision_id
	 * Routing for GET /$year/make_live/$revision_id
	 * 
	 * revisionable_item_id id can be ommited, resulting in that variable containing 
	 * the revision id instead. (Object assumed to have id 1 in this case)
	 *
	 * @param int    $year                  The year of the programme (not used, but to keep routing happy).
	 * @param int    $revisionable_item_id  The object ID we are pushing a revision live on.
	 * @param int    $revision_id           The revision ID we are putting live.
	 * @param int    $misc         			Only here to satisfy rules of overriding parent methods, so it isn't used.
	 */
	public function get_make_live($year, $revisionable_item_id = false, $revision_id = false, $misc = false)
	{
		return parent::get_make_live($year, URLParams::get_type(), $revisionable_item_id, $revision_id);
	}

	/**
	 * Routing for GET /$year/$object_id/revert_to_revision/$revision_id
	 * Routing for GET /$year/revert_to_revision/$revision_id
	 *
	 * @param int    $year         			The year of the programme (not used, but to keep routing happy).
	 * @param int    $revisionable_item_id  The object ID we are reverting to a revision on.
	 * @param int    $revision_id  			The revision ID we are reverting to.
	 * @param int    $misc         			Only here to satisfy rules of overriding parent methods, so it isn't used.
	 *
	 * @note revisionable_item_id id can be ommited, resulting in that varible containg 
	 *	the revision id instead. (Object assumed to have id 1 in this case)
	 */
	public function get_use_revision($year, $revisionable_item_id = false, $revision_id = false, $misc = false)
	{
		return parent::get_use_revision($year, URLParams::get_type(), $revisionable_item_id, $revision_id);
	}


	public function get_revert_to_previous($year, $revisionable_item_id = false, $revision_id = false, $misc = false)
	{
		return parent::get_revert_to_previous($year, URLParams::get_type(), $revisionable_item_id, $revision_id);
	}
	
	/**
	 * Routing for GET /$year/$object_id/unpublish/$revision_id
	 * Routing for GET /$year/unpublish/$revision_id
	 *
	 * @param int    $year         The year of the programme (not used, but to keep routing happy).
	 * @param int    $revisionable_item_id  The object ID we are pushing a revision live on.
	 * @param int    $revision_id  The revision ID we are putting live.
	 * @param int    $misc         	Only here to satisfy rules of overriding parent methods, so it isn't used.
	 *
	 * @note revisionable_item_id id can be ommited, resulting in that varible containg 
	 *	the revision id instead. (Object assumed to have id 1 in this case)
	 */
	public function get_unpublish($year, $revisionable_item_id = false, $revision_id = false, $misc = false)
	{
		return parent::get_unpublish($year, URLParams::get_type(), $revisionable_item_id, $revision_id);
	}

	/**
	 * Routing for GET /$year/{data_type}/rollback/{$itm_id}
	 *
	 * @param int    $year         The year of the programme (not used, but to keep routing happy).
	 * @param string $itm_id       The id of the item to show revisions for
	 * @param int    $misc         	Only here to satisfy rules of overriding parent methods, so it isn't used.
	*/
	public function get_view_revision($year, $itm_id = false, $revision_id = false, $misc = false)
	{
		return parent::get_view_revision($year, URLParams::get_type(), $item_id, $revision_id);
	}

	/**
	 * Routing for GET /$year/{data_type}/rollback/{$itm_id}
	 *
	 * @param int    $year         The year of the programme (not used, but to keep routing happy).
	 * @param string $itm_id       The id of the item to show revisions for
	 * @param int    $misc         Only here to satisfy rules of overriding parent methods, so it isn't used.
	*/
	public function get_rollback($year, $itm_id = false, $misc = false)
	{
		return parent::get_rollback($year, URLParams::get_type(), $itm_id);
	}
}

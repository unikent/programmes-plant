<?php

class ProgrammeSettings_Controller extends Revisionable_Controller {

	public $restful = true;
	public $views = 'programmesettings';
	protected $model = 'ProgrammeSetting';

	public $required_permissions = array("edit_overridable_data");

	/**
	 * Routing for /$year/$type/programmesettings
	 *
	 * @param int    $year The year.
	 * @param string $type Undergraduate or postgraduate.
	 */
	public function get_index($year, $type)
	{
		$model = $this->model;
		$data = $model::where('year', '=', $year)->first();

		if ($data == null)
		{
			return Redirect::to($year.'/'.$type.'/'.$this->views.'/create');
		} 
		else
		{
			return Redirect::to($year.'/'.$type.'/'.$this->views.'/edit');
		}

	}

	/**
	 * Our subject create function
	 *
	 */
	public function get_create($year, $type)
	{
		$this->data['fields'] = $this->get_fields();
		$this->data['create'] = true;
		$this->data['year'] = $year;

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
		$programmesetting = $model::where('year', '=', $year)->first();

		if(!$programmesetting) return Redirect::to($year.'/'.$type.'/'.$this->views);

		$this->data[$this->views] = $programmesetting ;

		$this->data['active_revision'] = $programmesetting->get_active_revision();

		$this->data['fields'] = $this->get_fields();
		$this->data['year'] = $year;

		$this->layout->nest('content', 'admin.'.$this->views.'.form', $this->data);
	}

	/**
	 * Routing for POST /$year/$type/create
	 *
	 * The change request page.
	 *
	 * @param int    $year The year of the created subject.
	 * @param string $type The type, either ug (undergraduate) or pg (postgraduate)
	 */
	public function post_create($year, $type)
	{

			$subject = new ProgrammeSetting;
			$subject->year = Input::get('year');

			// Save varible fields
			$f = $this->get_fields();
			foreach ($f as $c) {
				$col = $c->colname;
				$subject->$col = Input::get($col);
			}

			$subject->save();

			Messages::add('success','Programme settings created.');

			return Redirect::to($year.'/'.$type.'/'.$this->views.'/edit');

	}

	/**
	 * Routing for POST /$year/$type/edit
	 *
	 * Make a change.
	 *
	 * @param int    $year The year of the created subject.
	 * @param string $type The type, either ug (undergraduate) or pg (postgraduate)
	 */
	public function post_edit($year, $type)
	{

			$subject = ProgrammeSetting::where('year', '=', $year)->first();

			$subject->year = Input::get('year');

			//Save varible fields
			$f = $this->get_fields();
			foreach ($f as $c) {
				$col = $c->colname;
				$subject->$col = Input::get($col);
			}

			$subject->save();

			Messages::add('success','Programme settings updated.');

			return Redirect::to($year.'/'. $type.'/'. $this->views.'/edit');

	}

	private function get_fields()
	{
		$model = 'ProgrammeField';

		return  $model::where('active','=','1')->where_in('programme_field_type', array(ProgrammeField::$types['OVERRIDABLE_DEFAULT'], ProgrammeField::$types['DEFAULT']))->order_by('field_name','asc')->get();
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
		$programmesetting = ProgrammeSetting::where('year', '=', $year)->first();

		if (!$programmesetting) return Redirect::to($year.'/'.$type.'/'.$this->views);

		$revision = $programmesetting->find_revision($revision_id);
		
		if (!$revision) return Redirect::to($year.'/'.$type.'/'.$this->views);

		$revision_attributes = $programmesetting->attributes;
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
		$this->data['attributes'] = ProgrammeSetting::getAttributesList();

		$this->data['revision'] = $revision;
		$this->data['programmesetting'] = $programmesetting;

		return View::make('admin.'.$this->views.'.difference',$this->data);
	}

	/**
	 * Routing for GET /changes
	 *
	 * The change request page.
	 */
	public function get_changes()
	{
	   $this->data['revisions'] = DB::table('subjects_revisions')
			->where('status', '=', 'pending')
			->get();

		return View::make('admin.changes.index', $this->data);
	}

}
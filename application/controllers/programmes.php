<?php

class Programmes_Controller extends Revisionable_Controller {

	public $restful = true;
	public $views = 'programmes';
	protected $model = 'Programme';

	public $required_permissions = array("edit_own_programmes", "view_all_programmes", "edit_all_programmes");

	/**
	 * Routing for /$year/$type/programmes
	 *
	 * @param int    $year The year.
	 * @param string $type Undergraduate or postgraduate.
	 */
	public function get_index($year, $type)
	{
		$title_field = Programme::get_title_field();
		$award_field = Programme::get_award_field();
		$withdrawn_field = Programme::get_withdrawn_field();
		$suspended_field = Programme::get_suspended_field();
		$subject_to_approval_field = Programme::get_subject_to_approval_field();
		$subject_area_1 = Programme::get_subject_area_1_field();

		$model = $this->model;

		// Get user
		$user = Auth::user();
		// get required fields
		$fields_array = array('id', $title_field, $award_field, $withdrawn_field, $suspended_field, $subject_to_approval_field, 'live');

		// If user can view all programmes in system, get a list of all of them
		if($user->can("view_all_programmes"))
		{
			$programmes = $model::with('award')->where('year', '=', $year)->where('hidden', '=', false)->order_by($title_field)->get($fields_array);
		}
		elseif($user->can("edit_own_programmes"))
		{
			// If not, but user has permissions to edit own, show list of programmes with a subject area_1 assigned to the programme
			$programmes = $model::with('award')->where('year', '=', $year)->where('hidden', '=', false)->where_in($subject_area_1, explode(',', $user->subjects))->get($fields_array);
		}
		else
		{
			// Else empty list.
			$programmes = array();
		}

		
		$this->data[$this->views] = $programmes;

		$this->data['title_field'] = $title_field;
		$this->data['withdrawn_field'] = $withdrawn_field;
		$this->data['suspended_field'] = $suspended_field;
		$this->data['subject_to_approval_field'] = $subject_to_approval_field;

		$this->layout->nest('content', 'admin.'.$this->views.'.index', $this->data);
	}

	/**
	 * Present a form to allow ther creation of a new programme.
	 * If an item_id is passed, present the form prefilled with the item's values
	 *
	 * @param int    $year    The year
	 * @param string $type    Undergraduate or postgraduate.
	 * @param int    $item_id The ID of the programme to clone from.
	 */
	public function get_create($year, $type, $item_id = false)
	{
		$this->check_user_can("create_programmes");

		if ($item_id)
		{
			// We're cloning item_id
			$model = $this->model;
			$course = $model::find($item_id);
			$this->data['clone'] = true;
			$this->data['programme'] = $course;
		} 
		else 
		{
			$this->data['clone'] = false;
		}
		
		$this->data['sections'] = ProgrammeField::programme_fields_by_section();

		$this->data['create'] = true;
		$this->data['year'] = $year;

		$this->layout->nest('content', 'admin.'.$this->views.'.form', $this->data);
	}

	/**
	 * Routing for GET /$year/$type/edit/$programme_id
	 *
	 * @param int    $year    The year
	 * @param string $type    Undergraduate or postgraduate.
	 * @param int    $item_id The ID of the programme to edit.
	 */
	public function get_edit($year, $type, $id = false)
	{
		// Ensure we have a corresponding course in the database
		$programme = Programme::find($id);

		if (! $programme) return Redirect::to($year . '/' . $type . '/' . $this->views);

		$this->data['programme'] = $programme;
		
		$this->data['sections'] = ProgrammeField::programme_fields_by_section();
		$this->data['title_field'] = Programme::get_title_field();
		$this->data['year'] = $year;
		$this->data['active_revision'] = $programme->get_active_revision(array('id','status','programme_id', 'year', 'edits_by', 'published_at','created_at'));

		//dd($this->data['active_revision']);
		//Get lists data
		$this->layout->nest('content', 'admin.'.$this->views.'.form', $this->data);
	}

	/**
	 * Routing for POST /$year/$type/create
	 *
	 * The change request page.
	 *
	 * @param int    $year The year of the created programme.
	 * @param string $type The type, either ug (undergraduate) or pg (postgraduate)
	 */
	public function post_create($year, $type)
	{
		
		$this->check_user_can("create_programmes");

		// placeholder for any future validation rules
		$rules = array(
		);
		$validation = Validator::make(Input::all(), $rules);
		if ($validation->fails()) 
		{
			Messages::add('error',$validation->errors->all());
			return Redirect::to($year.'/'.$type.'/'.$this->views.'/create')->with_input();
		} 
		else 
		{
			$programme = new Programme;
			$programme->year = Input::get('year');
			$programme->created_by = Auth::user()->username;
			
			// get the programme fields
			$programme_fields = ProgrammeField::programme_fields();
			
			// assign the input data to the programme fields
			$programme_modified = ProgrammeField::assign_fields($programme, $programme_fields, Input::all());
			
			// save the modified programme data
			$programme_modified->save();
			
			// success message
			Messages::add('success','Programme added');
			
			// redirect back to the same page
			return Redirect::to($year.'/'.$type.'/'.$this->views.'/edit/'.$programme->id);
		}
	}

	/**
	 * Routing for POST /$year/$type/edit
	 *
	 * Make a change.
	 *
	 * @param int    $year The year of the created programme
	 * @param string $type The type, either ug (undergraduate) or pg (postgraduate)
	 */
	public function post_edit($year, $type)
	{
		// placeholder for any future validation rules
		$rules = array(
		);
		$validation = Validator::make(Input::all(), $rules);
		if ($validation->fails())
		{
			Messages::add('error',$validation->errors->all());
			return Redirect::to($year.'/'.$type.'/'.$this->views.'/edit/')->with_input();
		} 
		else 
		{
			$programme = Programme::find(Input::get('programme_id'));
			$programme->year = Input::get('year');

			// get the programme fields
			$programme_fields = ProgrammeField::programme_fields();
			
			// assign the input data to the programme fields
			$programme_modified = ProgrammeField::assign_fields($programme, $programme_fields, Input::all());

			// save the modified programme data
			$programme_modified->save();
			
			// success message
			$title_field = Programme::get_title_field();
			Messages::add('success', "Saved ".$programme->$title_field);
			
			// redirect back to the same page we were on
			return Redirect::to($year.'/'. $type.'/'. $this->views.'/edit/'.$programme->id);
		}
	}

	/**
	 * Routing for GET /$year/$type/programmes/$programme_id/difference/$revision_id
	 *
	 * @param int    $year         The year of the programme (not used, but to keep routing happy).
	 * @param string $type         The type, either undegrad/postgrade (not used, but to keep routing happy).
	 * @param int    $programme_id The programme ID we are promoting a given revision to be live.
	 * @param int    $revision_id  The revision ID we are promote to the being the live output for the programme.
	 */
	public function get_difference($year, $type, $programme_id = false, $revision_id = false)
	{
		if (! $programme_id) return Redirect::to($year.'/'.$type.'/'.$this->views);

		// Get revision specified
		$programme = Programme::find($programme_id);

		if (! $programme) return Redirect::to($year.'/'.$type.'/'.$this->views);

		$revision = $programme->get_revision($revision_id);

		if (! $revision) return Redirect::to($year.'/'.$type.'/'.$this->views);

		$programme_attributes = $programme->attributes;
		$revision_for_diff = $revision->attributes;

		// Ignore these fields which will always change.
		foreach (array('id', 'created_by', 'published_by', 'created_at', 'updated_at', 'live') as $ignore) 
		{
			unset($revision_for_diff[$ignore]);
			unset($programme_attributes[$ignore]);
		}

		$schools = School::all_as_list();
		$sub = Programme::all_as_list();
		$pro = Programme::all_as_list();

		//$revision_for_diff['related_school_ids'] = $this->splitToText($revision_for_diff['related_school_ids'],$schools);
		//$programme_attributes['related_programme_ids'] = $this->splitToText($programme_attributes['related_programme_ids'],$sub);
		//$revision_for_diff['related_programme_ids'] = $this->splitToText($revision_for_diff['related_programme_ids'],$sub);
		//$programme_attributes['related_programme_ids'] = $this->splitToText($programme_attributes['related_programme_ids'],$pro);
		//$revision_for_diff['related_programme_ids'] = $this->splitToText($revision_for_diff['related_programme_ids'],$pro);

		$differences = array_diff_assoc($programme_attributes, $revision_for_diff);

		$diff = array();

		foreach ($differences as $field => $value) 
		{
			$diff[$field] = SimpleDiff::htmlDiff($programme_attributes[$field], $revision_for_diff[$field]);
		}

		$this->data['diff'] = $diff;
		$this->data['new'] = $revision_for_diff;
		$this->data['old'] = $programme_attributes;

		$this->data['attributes'] = Programme::get_attributes_list();

		$this->data['revision'] = $revision;
		$this->data['programme'] = $programme;

		return View::make('admin.'.$this->views.'.difference',$this->data);
	}


	/**
	 * Routing for GET /preview/$programme_id/preview/$revision_id
	 *
	 * @param int    $revisionable_item_id  The object ID we are reverting to a revision on.
	 * @param int    $revision_id  The revision ID we are reverting to.
	 *
	 */
	public function get_preview($year, $type, $programme_id, $revision_id)
	{
		// Create preview and grab hash
		$hash = API::create_preview($programme_id, $revision_id);
		if($hash !== false){
			return Redirect::to(Config::get('application.front_end_url')."preview/".$hash);	
		}
	}



	private function splitToText($list,$options)
	{
		if($list == '' || $list == null) return '';

		$list = explode(',',$list);
		$l_str = '';
		foreach ($list as $val) {
				$l_str .= $options[$val].', ';

		}

		return $l_str;
	}

	/**
	 * Routing for GET /changes
	 *
	 * The change request page.
	 */
	public function get_changes()
	{
		$this->data['revisions'] = DB::table('programmes_revisions')
			->where('status', '=', 'pending')
			->get();

		return View::make('admin.changes.index', $this->data);
	}

}
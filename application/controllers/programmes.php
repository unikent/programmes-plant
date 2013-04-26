<?php

// use user and role objects from namespace
use \Verify\Models\user;

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
		$fields_array = array('id', $title_field, $award_field, $withdrawn_field, $suspended_field, $subject_to_approval_field, 'live', 'locked_to');

		// If user can view all programmes in system, get a list of all of them
		if($user->can("view_all_programmes"))
		{
			$programmes = $model::with('award')->where('year', '=', $year)->where('hidden', '=', false)->order_by($title_field)->get($fields_array);
		}
		elseif($user->can("edit_own_programmes"))
		{
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

		// Get either the active revision, or the review under_review.
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
		$this->check_user_can('recieve_edit_requests');

		if (!$programme_id) return Redirect::to($year.'/'.$type.'/'.$this->views);

		// Get programme
		$programme = Programme::find($programme_id);
		if (!$programme) return Redirect::to($year.'/'.$type.'/'.$this->views);

		$revisions = array(
			'live' => $programme->get_live_revision(),
			'proposed' => $programme->get_revision($revision_id),
		);
		if (empty($revisions['live']) || empty($revisions['proposed'])) return Redirect::to($year.'/'.$type.'/'.$this->views);

		$attributes = array(
			'all' => array_keys($programme->attributes), // By getting attributes this way we can get non-programmatic attributes too
			'ignore' => array('id', 'created_by', 'published_by', 'created_at', 'updated_at', 'live'), // Use human friendly atttibute names (e.g. strip _ID)
			'nodiff' => array(), // Use human friendly atttibute names (e.g. strip _ID)
			'resolved' => array(),
		);

		$attribute_map = Programme::get_attributes_list(); // This will only return programmatic atributes

		// Iterate over attributes 'all'...
		foreach($attributes['all'] as $key => $value)
		{
			// ...update them to make their various representations available
			$attribute = array(
				'machine' => $value,
				'field' => Revisionable::trim_id_from_field_name($value),
				'label' => isset($attribute_map[$value]) ? $attribute_map[$value] : __('programmes.' . $value),
			);

			// ...remap array to be keyed by attribute machine name
			unset($attributes[$key]);
			$attributes['all'][$attribute['machine']] = $attribute;

			// ...if they are not in the 'ignored' array, load their values for each revision
			if(!in_array($attribute['field'], $attributes['ignore']))
			{
				// Load the given attribute for each revision, gracefully resolving relational attributes
				$resolved = array(
					'live' => is_object($revisions['live']->{$attribute['field']}) ? $revisions['live']->{$attribute['field']}->name : $revisions['live']->{$attribute['machine']},
					'proposed' => is_object($revisions['proposed']->{$attribute['field']}) ? $revisions['proposed']->{$attribute['field']}->name : $revisions['proposed']->{$attribute['machine']},
				);

				// ...compare the resolved attribute values, only retain them if they have changed
				if($resolved['live'] !== $resolved['proposed'])
				{
					// ...restrict the diffing of fields (step not necessary if field is already excluded)
					if(!in_array($attribute['field'], $attributes['nodiff']))
					{
						// ...only diff fields if they are (a) non relational, or if they (b) don't contain spaces
						if(is_object($revisions['proposed']->{$attribute['field']}) || !preg_match('/(\s){1,}/', $resolved['proposed']))
						{
							$attributes['nodiff'][] = $attribute['field'];
						}						
					}

					$attributes['resolved'][$attribute['machine']] = $resolved;
				}
			}
		}

		$data = array(
			'programme' => $programme,
			'revisions' => $revisions,
			'attributes' => $attributes,
 		);

		return $this->layout->nest('content', 'admin.'.$this->views.'.difference', $data);
	}

	/**
	 * Routing for GET /$year/$type/$object_id/submit_programme_for_editing/$revision_id
	 * 
	 * @param int    $year         The year of the object (not currently used).
	 * @param string $type         The type of programme, either ug or pg (not currently used).
	 * @param int    $revision_id  The ID of the revision being submitted for editing.
	 */
	public function get_submit_programme_for_editing($year, $type, $object_id, $revision_id)
	{
		$this->check_user_can('submit_programme_for_editing');

		$programme = Programme::find($object_id);
		$revision = ProgrammeRevision::find($revision_id);

		if(!$programme || !$revision) return Redirect::to($year.'/'.$type.'/'.$this->views);
		$programme->submit_revision_for_editing($revision->id);

		// Send email notification to the approval list
		if(Config::get('programme_revisions.notifications.on')){
			$author = Auth::user();
			$title = $programme->{Programme::get_title_field()};

			$mailer = IoC::resolve('mailer');

			$message = Swift_Message::newInstance("New programme update for {$title}")
				->setFrom(Config::get('programme_revisions.notifications.from'))
				->setTo(Config::get('programme_revisions.notifications.to'))
				->addPart("{$author->fullname} has submitted a new programme update for {$title}, which is currently pending approval.", 'text/plain');

			$mailer->send($message);
		}

		Messages::add('success', "Revision of " . $revision->{Programme::get_title_field()} . " been sent to EMS for editing, thank you.");
		return Redirect::to($year . '/' . $type . '/' . $this->views);
	}

	/**
	 * Routing for POST /$year/$type/programmes/approve_revision
	 *
	 * @param int    $year         The year of the programme (not used, but to keep routing happy).
	 * @param string $type         The type, either undegrad/postgrade (not used, but to keep routing happy).
	 */
	public function post_approve_revision($year, $type)
	{
		$this->check_user_can('make_programme_live');

		$programme_id = Input::get('programme_id');
		$revision_id = Input::get('revision_id');

		if (!$programme_id) return Redirect::to($year.'/'.$type.'/'.$this->views);

		$programme = Programme::find($programme_id);
		$revision = ProgrammeRevision::find($revision_id);
		if (!$programme || !$revision) return Redirect::to($year.'/'.$type.'/'.$this->views);

		if ($programme->make_revision_live((int) $revision_id))
		{
			if(Config::get('programme_revisions.notifications.on')){
				$author = User::where('username', '=', $revision->edits_by)->first(array('email', 'fullname'));
				$title = $programme->{Programme::get_title_field()};

				$mailer = IoC::resolve('mailer');

				$message = Swift_Message::newInstance("Your programme update was approved")
					->setFrom(Config::get('programme_revisions.notifications.from'))
					->setTo($author->email)
					->addPart("Dear {$author->fullname}, \n\nYour update to {$title} has now been approved. \n\nMany thanks!", 'text/plain');

				$mailer->send($message);
			}

			Messages::add('success', 'Revision was approved');
		}
		else
		{
			Messages::add('error', 'Revision could not be approved at this time.');
		}

		return Redirect::to($year.'/'.$type.'/'.$this->views);
	}

	/**
	 * Routing for POST /$year/$type/programmes/request_changes
	 *
	 * @param int    $year         The year of the programme (not used, but to keep routing happy).
	 * @param string $type         The type, either undegrad/postgrade (not used, but to keep routing happy).
	 */
	public function post_request_changes($year, $type)
	{
		$this->check_user_can('request_changes');

		$programme_id = Input::get('programme_id');
		$revision_id = Input::get('revision_id');
		if (!$programme_id || !$revision_id) return Redirect::to($year.'/'.$type.'/'.$this->views);

		// Load the Programme
		$programme = Programme::find($programme_id);
		if (empty($programme)) return Redirect::to($year.'/'.$type.'/'.$this->views);

		// Load the Revision
		$revision = ProgrammeRevision::find($revision_id);
		if (empty($revision)) return Redirect::to($year.'/'.$type.'/'.$this->views);

		// Load the message
		$body = Input::get('message');
		if(!empty($body))
		{
			$author = User::where('username', '=', $revision->edits_by)->first(array('email', 'fullname'));
			$title = $programme->{Programme::get_title_field()};

			$mailer = IoC::resolve('mailer');

			$message = Swift_Message::newInstance("RE: Your updates to {$title}")
				->setFrom(Config::get('programme_revisions.notifications.from'))
				->setTo($author->email)
				->addPart(strip_tags($body), 'text/plain')
				->setBody($body,'text/html');

			$mailer->send($message);
		}

		return Redirect::to($year.'/'.$type.'/'.$this->views);
	}

	/**
	 * Routing for POST /$year/$type/programmes/reject_revision
	 *
	 * @param int    $year         The year of the programme (not used, but to keep routing happy).
	 * @param string $type         The type, either undegrad/postgrade (not used, but to keep routing happy).
	 */
	public function post_reject_revision($year, $type)
	{
		$this->check_user_can('revert_revisions');

		$programme_id = Input::get('programme_id');
		$revision_id = Input::get('revision_id');

		if (!$programme_id) return Redirect::to($year.'/'.$type.'/'.$this->views);

		$programme = Programme::find($programme_id);
		if (!$programme) return Redirect::to($year.'/'.$type.'/'.$this->views);

		if ($programme->revert_to_previous_revision((int) $revision_id))
		{
			Messages::add('success', 'Revision was rejected');
		}
		else
		{
			Messages::add('error', 'Revision could not be rejected at this time.');
		}

		return Redirect::to($year.'/'.$type.'/'.$this->views);
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
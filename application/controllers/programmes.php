<?php

class Programmes_Controller extends Revisionable_Controller {

	protected $model = '';
	public $views = 'programmes';
	public $restful = true;

	public $required_permissions = array("edit_own_programmes", "view_all_programmes", "edit_all_programmes");

	// Determine correct model (PG / UG)
	public function __construct()
	{  	
		$this->model = (URI::segment(2)=='ug') ? 'UG_Programme' : 'PG_Programme';

		// Construct parent.
		parent::__construct();
	}
	
	public function get_index($year='')
	{
		$this->layout->nest('content', 'admin.index');
	}

	/**
	 * Routing for /$year/$type/programmes
	 *
	 * @param int    $year The year.
	 * @param string $type Undergraduate or postgraduate.
	 */
	public function get_list($year, $type)
	{

		$model = $this->model;

		// get fields
		$title_field 		= 	$model::get_programme_title_field();
		$award_field 		= 	$model::get_award_field();
		$withdrawn_field 	= 	$model::get_programme_withdrawn_field();
		$suspended_field 	= 	$model::get_programme_suspended_field();
		$subject_to_approval_field = $model::get_subject_to_approval_field();
		$subject_area_1 	= 	$model::get_subject_area_1_field();

		// Get user
		$user = Auth::user();

		// get required fields
		$fields_array = array('id', $title_field, $award_field, $withdrawn_field, $suspended_field, $subject_to_approval_field, 'locked_to', 'live_revision', 'current_revision' , 'updated_at','instance_id');

		// If user can view all programmes in system, get a list of all of them
		if($user->can("view_all_programmes"))
		{
			$programmes = $model::where('year', '=', $year)->where('hidden', '=', false)->get($fields_array);
		}
		elseif($user->can("edit_own_programmes"))
		{
			$subject_field = URLparams::$type.'_subjects';
			$programmes = $model::where('year', '=', $year)->where('hidden', '=', false)->where_in($subject_area_1, explode(',', $user->{$subject_field} ))->get($fields_array);
		}
		else
		{
			// Else empty list.
			$programmes = array();
		}
		
		$this->data[$this->views] = $programmes;

		$this->data['year'] = $year;
		$this->data['title_field'] = $title_field;
		$this->data['award_field'] = $award_field;
		$this->data['withdrawn_field'] = $withdrawn_field;
		$this->data['suspended_field'] = $suspended_field;
		$this->data['subject_to_approval_field'] = $subject_to_approval_field;

		$this->layout->nest('content', 'admin.programmes.index', $this->data);
	}

	/**
	 * Present a form to allow ther creation of a new programme.
	 * If an item_id is passed, present the form prefilled with the item's values
	 *
	 * @param int    $year    The year
	 * @param string $type    Undergraduate or postgraduate.
	 * @param int    $item_id The instance ID of the programme to clone from.
	 */
	public function get_create($year, $type, $item_id = false)
	{
		$this->check_user_can("create_programmes");

		if ($item_id)
		{
			// We're cloning item_id
			$model = $this->model;
			$course = $model::where('instance_id', '=', $item_id)->where('year', '=', $year)->first();
			$this->data['clone'] = true;
			$this->data['programme'] = $course;
		} 
		else 
		{
			$this->data['clone'] = false;
		}
		
		$fieldModel = $this->model.'Field';
		$this->data['sections'] = $fieldModel::programme_fields_by_section();
		$this->data['model'] = $this->model;
		$this->data['create'] = true;
		$this->data['year'] = $year;


		$this->layout->nest('content', 'admin.'.$this->views.'.form', $this->data);
	}

	/**
	 * Routing for GET /$year/$type/edit/$instance_id
	 *
	 * controls the display of the programme edit form
	 *
	 * @param int    $year    The year
	 * @param string $type    Undergraduate or postgraduate.
	 * @param int    $item_id The instance ID of the programme to edit.
	 */
	public function get_edit($year, $type, $id = false)
	{	
		$fieldModel = $this->model.'Field';
		$model = $this->model;

		// Ensure we have a corresponding course in the database
		$programme = $model::where('instance_id', '=', $id)->where('year', '=', $year)->first();

		if (! $programme) return Redirect::to($year . '/' . $type . '/' . $this->views);
		
		// get the appropriate data to display on the programme form
		$this->data['programme'] = $programme;
		$this->data['model'] = $model;
		$this->data['sections'] = $fieldModel::programme_fields_by_section();

		$this->data['title_field'] = $model::get_programme_title_field();
		$this->data['year'] = $year;

		// get the active revision
		$this->data['active_revision'] = $programme->get_active_revision(array('id','status','programme_id', 'year', 'edits_by', 'under_review', 'published_at','created_at'));
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
		
		$fieldModel = $this->model.'Field';
		$model = $this->model;


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
			$programme = new $model;
			$programme->year = Input::get('year');
			$programme->created_by = Auth::user()->username;
			
			// get the programme fields
			$programme_fields = $fieldModel::programme_fields();
			
			// assign the input data to the programme fields
			$programme_modified = $fieldModel::assign_fields($programme, $programme_fields, Input::all());
			
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

		$fieldModel = $this->model.'Field';
		$model = $this->model;


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
			$programme = $model::find(Input::get('programme_id'));
		
			$programme->year = Input::get('year');
			
			// get the programme fields
			$programme_fields = $fieldModel::programme_fields();
		
			// assign the input data to the programme fields
			$programme_modified = $fieldModel::assign_fields($programme, $programme_fields, Input::all());

			// save the modified programme data
			$programme_modified->save();
			
			// success message
			$title_field = $model::get_title_field();
			Messages::add('success', "Saved ".$programme->$title_field);
			
			// redirect back to the same page we were on
			return Redirect::to($year.'/'. $type.'/'. $this->views.'/edit/'.$programme->instance_id);
		}
	}

	/**
	 * Routing for GET /$year/$type/programmes/$programme_id/difference/$revision_id
	 *
	 */
	public function get_review($year, $type, $programme_id = false, $revision_id = false)
	{
		$this->check_user_can('recieve_edit_requests');
		return $this->diff_revisions($year, $type, $programme_id, $revision_id, 'review');
	}
	/**
	 * Routing for GET /$year/$type/programmes/$programme_id/difference/$revision_id
	 *
	 */
	public function get_difference($year, $type, $programme_id = false, $revision_id = false)
	{
		$this->check_user_can('recieve_edit_requests');
 		return $this->diff_revisions($year, $type, $programme_id, $revision_id, 'difference');
	}
	/*
	 * Shared route between  get_difference & get_review
	 *
	 * @param int    $year         The year of the programme (not used, but to keep routing happy).
	 * @param string $type         The type, either undegrad/postgrade (not used, but to keep routing happy).
	 * @param int    $programme_id The programme ID we are promoting a given revision to be live.
	 * @param int    $revision_id  The revision ID we are promote to the being the live output for the programme.
	 */
	protected function diff_revisions($year, $type, $programme_id = false, $revision_id = false, $view_name = 'difference'){
		$model = $this->model ;

		if (!$programme_id) return Redirect::to($year.'/'.$type.'/'.$this->views);

		// Get programme
		$programme = $model::find($programme_id);
		if (!$programme) return Redirect::to($year.'/'.$type.'/'.$this->views);

		$live_revision = $programme->find_live_revision();
		$revision = $programme->get_revision($revision_id);

		// if there is not yet a live revision get the difference with our modified revision only
		if(empty($live_revision) && !empty($revision)){
			$diff = $model::revision_diff($revision, null);

			$data = array(
				'programme' => $programme,
				'diff' => $diff
			);

			return $this->layout->nest('content', 'admin.'.$this->views.'.'.$view_name.'_pre_live', $data);
		}
		else{
			//Get diff data
			$diff = $model::revision_diff($live_revision,  $revision);
			if ($diff==false) return Redirect::to($year.'/'.$type.'/'.$this->views);

			$data = array(
				'diff' => $diff,
				'programme' => $programme
			);

 			return $this->layout->nest('content', 'admin.'.$this->views.'.'.$view_name, $data);
		}
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

		$model = $this->model;
		$revision_model = $model::$revision_model;


		$programme = $model::find($object_id);
		$revision = $revision_model::find($revision_id);

		if(!$programme || !$revision) return Redirect::to($year.'/'.$type.'/'.$this->views);
		$programme->submit_revision_for_editing($revision->id);

		// Send email notification to the approval list
		if(Config::get('programme_revisions.notifications.on')){
			$author = Auth::user();
			$title = $programme->{$model::get_title_field()};

			// get the awards
			$awards = static::get_awards_string($programme, $type);

			$mailer = IoC::resolve('mailer');

			// append 'TEST' to email titles when on the test server environment
			$title = (Request::env() == 'test') ? 'TEST - ' . $title : $title;

			$message = Swift_Message::newInstance(__('emails.admin_notification.title', array('title' => $title, 'awards' => $awards)))
				->setFrom(Config::get('programme_revisions.notifications.from'))
				->setTo(Config::get('programme_revisions.notifications.to'))
				->addPart(__('emails.admin_notification.body', array('author' => $author->fullname, 'title' => $title, 'awards' => $awards, 'link_to_inbox' => HTML::link_to_action('editor@inbox', __('emails.admin_notification.pending_approval_text')))), 'text/html');

			$mailer->send($message);
		}

		Messages::add('success', "Revision of " . $revision->{$model::get_title_field()} . " been sent to EMS for editing, thank you.");
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

		$model = $this->model;
		$revision_model = $model::$revision_model;

		$programme_id = Input::get('programme_id');
		$revision_id = Input::get('revision_id');

		if (!$programme_id) return Redirect::to($year.'/'.$type.'/'.$this->views);

		$programme = $model::find($programme_id);
		$instance_id = $programme->instance_id;

		$revision = $revision_model::find($revision_id);
		if (!$programme || !$revision) return Redirect::to($year.'/'.$type.'/'.$this->views);

		if ($programme->make_revision_live((int) $revision_id))
		{
			if(Config::get('programme_revisions.notifications.on')){
				$author = User::where('username', '=', $revision->edits_by)->first(array('email', 'fullname'));
				$title = $programme->{$model::get_title_field()};

				// append 'TEST' to email titles when on the test server environment
				$title = (Request::env() == 'test') ? 'TEST - ' . $title : $title;

				$slug = $programme->{$model::get_slug_field()};

				// get the awards
				$awards = static::get_awards_string($programme, $type);

				$link_to_edit_programme = HTML::link($year.'/'.$type.'/'.$this->views.'/'.'edit/'.$programme->id, $title . ' ' . $awards);

				$ugpg = ( $type == 'pg') ? 'postgraduate' : 'undergraduate';
				
				$link_to_programme_frontend = Config::get('application.front_end_url') . $ugpg . '/' . $instance_id . '/' . $slug;
				$link_to_programme_frontend = HTML::link($link_to_programme_frontend, $link_to_programme_frontend);

				$mailer = IoC::resolve('mailer');

				$message = Swift_Message::newInstance(__('emails.user_notification.approve.title', array('title' => $title, 'awards' => $awards)))
					->setFrom(Config::get('programme_revisions.notifications.from'))
					->setTo($author->email)
					->addPart(
						__('emails.user_notification.approve.body', array(
							'author' => $author->fullname, 
							'title' => $title, 
							'id' => $programme->id, 
							'slug' => $slug, 
							'link_to_edit_programme' => $link_to_edit_programme,
							'link_to_programme_frontend' => $link_to_programme_frontend
						)), 
						'text/html'
					);

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

		$model = $this->model;
		$revision_model = $model::$revision_model;

		$programme_id = Input::get('programme_id');
		$revision_id = Input::get('revision_id');
		if (!$programme_id || !$revision_id) return Redirect::to($year.'/'.$type.'/'.$this->views);

		// Load the Programme
		$programme = $model::find($programme_id);
		if (empty($programme)) return Redirect::to($year.'/'.$type.'/'.$this->views);

		// Load the Revision
		$revision = $revision_model::find($revision_id);
		if (empty($revision)) return Redirect::to($year.'/'.$type.'/'.$this->views);

		// Load the message
		$body = Input::get('message');
		if(!empty($body))
		{
			$author = User::where('username', '=', $revision->edits_by)->first(array('email', 'fullname'));
			$title = $programme->{$model::get_title_field()};

			// append 'TEST' to email titles when on the test server environment
			$title = (Request::env() == 'test') ? 'TEST - ' . $title : $title;

			// get the awards
			$awards = static::get_awards_string($programme, $type);

			$link = URL::to_action($year.'/'.$type.'/'.'programmes@edit', array($programme_id));
			$body = __('emails.user_notification.request.body', array('title' => $title, 'awards' => $awards, 'link' => $link)) . $body;

			$mailer = IoC::resolve('mailer');
			$message = Swift_Message::newInstance(__('emails.user_notification.request.title', array('title' => $title, 'awards' => $awards)))
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

		$model = $this->model;

		if (!$programme_id) return Redirect::to($year.'/'.$type.'/'.$this->views);

		$programme = $model::find($programme_id);
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
	public function get_preview($year, $level, $programme_id, $revision_id)
	{
		$level = ( $level == 'pg') ? 'postgraduate' : 'undergraduate';
		// Create preview and grab hash
		$hash = API::create_preview($programme_id, $revision_id);
		if($hash !== false){
			return Redirect::to(Config::get('application.front_end_url').$level."/preview/".$hash);	
		}
	}

	/**
	 * Routing for GET /simpleview/$programme_id/simpleview/$revision_id
	 *
	 * @param int    $revisionable_item_id  The object ID we are reverting to a revision on.
	 * @param int    $revision_id  The revision ID we are reverting to.
	 *
	 */
	public function get_simpleview($year, $level, $programme_id, $revision_id)
	{
		$level = ( $level == 'pg') ? 'postgraduate' : 'undergraduate';
		// Create simpleview and grab hash
		$hash = API::create_preview($programme_id, $revision_id);
		if($hash !== false){
			return Redirect::to(Config::get('application.front_end_url').$level."/simpleview/".$hash);	
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

	/**
	 * Show programme deliveries
	 */
	public function get_deliveries($year, $type, $id){

		// Delete a programme if delete param is passed
		if(isset($_GET['delete'])){
			// ensure perms
			if(Auth::user()->can("edit_pg_deliveries")){
				PG_Delivery::find(Input::get('id'))->delete();
			}
			
			return Redirect::to(URI::current());
		}

		$model = $this->model;

		$deliveries = $model::find($id)->get_deliveries();
		
		return View::make('admin.programmes.deliveries', array('deliveries' => $deliveries, 'type'=>$type));
	}

	/**
	 * update programme deliveries (PG only)
	 */
	public function post_deliveries($year, $type, $id){


		if(Input::get('id')){
			$delivery = PG_Delivery::find(Input::get('id'));
		}else{
			$delivery = new PG_Delivery;
			$delivery->programme_id = $id;
		}
		
		$delivery->award = Input::get('award');
		$delivery->pos_code = Input::get('pos_code');
		$delivery->mcr = Input::get('mcr');
		$delivery->description = Input::get('description');
		$delivery->attendance_pattern = Input::get('attendance_pattern');
		
		$delivery->save();

		return Redirect::to(URI::current());	
	}

	/**
	* get the awards as a string
	*/
	public function get_awards_string($programme, $type)
	{
		
		$awards = '';
		if ($type == 'pg')
		{
			$award_field = PG_Programme::get_award_field();
			$awards = PG_Award::replace_ids_with_values($programme->$award_field, false, true);
			$awards = implode(', ', $awards);
		}
		else
		{
			$award_field = UG_Programme::get_award_field();
			$awards = UG_Award::replace_ids_with_values($programme->$award_field, false, true);
			$awards = $awards[0];
		}
		return $awards;
	}

}
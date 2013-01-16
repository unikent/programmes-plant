<?php
/**
 * Revisionable.
 * 
 * A model for datatypes that need to maintain revisions of itself.
 */
class Revisionable extends SimpleData {

	// Revision model (name of model for revisions of this type)
	protected $revision_model = false;

	// Data Type (Programme, Global, etc)
	protected $data_type = false;
	
	// Id used to link items of datatype (optional)
	protected $data_type_id = false;

	// Does this model seperate items by year? (false by default, although true for (all|most) revisionble types)
	public static $data_by_year = true;

	/**
	 * Create new instance of a revisionble object
	 *
	 * @param $attributes Attributes to be added to new instance of this model
	 * @param $exists Does this object already exist in the DB
	 */
	public function __construct($attributes = array(), $exists = false)
	{
		// Use called class to deterime datatype
		$this->data_type = get_called_class();
		// If not set in parent, just assume modelRevision as name
		if(!$this->revision_model)  $this->revision_model = $this->data_type .'Revision';
		if(!$this->data_type_id) $this->data_type_id = $this->data_type;

		// Pass to real constructor
		parent::__construct($attributes, $exists);

		// Ensure default status is 0
		$this->live = 0;
	}

	/**
	 * Save instance of this object along with a revision.
	 *
	 * @return true|false success
	 */
	public function save()
	{
		if (!$this->dirty()) return true;

		// Toggle live status (0=unpublished, if has been published set to 1 = changes)
		if ($this->live != 0)  $this->live = 1;

		// Save self.
		$success = parent::save();
		// If the save succeeds, save a new revision and return its status 
		// (so the return from this save() is means both the revision & save itself were successful.)
		if($success) return $this->save_revision();

		return false;
	}

	/**
	 * Save revision of current item
	 *
	 * @return true|false success
	 */
	private function save_revision()
	{
		$revision_model = $this->revision_model;

		// Get new revision instance
		$revision = new $revision_model;

		// Get attributes
		$revision_values = $this->attributes;
		unset($revision_values['id']);
		unset($revision_values['created_by']);
		unset($revision_values['live']);

		// @todo @workaround for revisionble tests.
		//
		// The case for using mocking. 
		//
		// SQLite doesn't supporting dropping columns once they are created, meaning 
		// that although "published_by" doesnt exist anymore, in the test enviroment 
		// it lingers on. This then causes errors when a revisionble item is saved 
		// from a test as eloquent picks up the now non-existent published_by column 
		// from SQLite and attempts to add it to the still none existent column of 
		// the same name in the revision item, which (by virtue of not existing) 
		// causes an SQL error and the test to fail. 
		//
		// This extra unset fixes this bug but is not an ideal fix for the long term.
		unset($revision_values['published_by']);

		// Timestamp revision & set editor etc.
		$revision_values['created_at'] = $this->updated_at;
		$revision_values['updated_at'] = $revision_values['created_at'];
		$revision_values['edits_by'] = Auth::user();

		// Status = selected
		$revision_values['status'] = 'selected';

		// Set Programme ID so we know which programme this revision belongs to.
		$revision_values[$this->data_type_id.'_id'] = $this->id;

		// Set the data in to the revision
		$revision->fill($revision_values);

		// Set previous revision back to draft
		$revision_model::where($this->data_type_id.'_id','=',$this->id)->where('status','=','selected')->update(array('status'=>'draft'));	

		// Save revision
		return $revision->save();
	}

	/**
	 * Get all revisions for this item (or revisions in a particular status if status is passed)
	 * @param $status status of revisions to return
	 * @return array of revisions
	 */
	public function get_revisions($status = false)
	{
		$model = $this->revision_model;
		// store query obj
		$query = $model::where($this->data_type_id.'_id','=',$this->id);
		// if status is set add filter
		if($status)$query = $query->where('status', '=', $status);
		// return data
		return $query->order_by('created_at', 'desc')->get();
	}

	/**
	 * Get a particular revision
	 *
	 * @param $id of revision
	 * @return revision instance
	 */
	public function get_revision($revision_id)
	{
		$model = $this->revision_model;
		return $model::find($revision_id);
	}

	/**
	 * Get currently active revision
	 * @return active revision instance
	 */
	public function get_active_revision()
	{
		// If all is up to date (live=2) return live/selected item
		// else get item marked as selected
		$model = $this->revision_model;

		if ($this->live == 2)
		{
			return $model::where($this->data_type_id.'_id','=',$this->id)->where('status','=','live')->first();
		}
		else
		{
			return $model::where($this->data_type_id.'_id','=',$this->id)->where('status','=','selected')->first();
		}
	}	

	/**
	 * make a revision of this item live.
	 * 
	 * @param $revision Object|id
	 * @return $revision
	 */
	public function make_revision_live($revision)
	{
		// If its an id, convert to actual revision
		if (is_int($revision))
		{
			$revision = $this->get_revision($revision);
		}

		// Get the currently "active/selected" revision.
		// If this revision is currently live, change its status to selected (so the system knows which revision is being edited/used)
		// If the active revision is already current, leave it be
		$active_revision = $this->get_active_revision();
		if($active_revision->status == 'live'){
			$active_revision->status = 'selected';
			$active_revision->save();
		}

		//If revision being made live us cyrrent, set item status to say there are no later versions
		if($revision->status == 'selected'){
			// Update the 'live' setting in the main item (not the revision) so it's marked as latest version published to live (ie 2)
			$this->live = '2';
		}else{
			// If the revision going live isn't the current, ensure system knows
			// there are still later revisions
			$this->live = '1';
		}
		parent::save();
		
		// Set previous live to a non-live status (prior_live so we can tell them apart from drafts that have never been used)
		$revision_model = $this->revision_model;
		$revision_model::where($this->data_type_id.'_id','=',$this->id)->where('status','=','live')->update(array('status'=>'prior_live'));

		// Update and save this revision 
		$revision->status = 'live';
		$revision->published_at = date('Y-m-d H:i:s');
		$revision->made_live_by = Auth::user();
		$revision->save();

		// Update feed file
		$this->generate_feed_file($revision);

		// Return result
		return $revision;
	}

	/**
	 * revert "current" revision to the previous one
	 * 
	 * @param $revision Object|id
	 * @return $revision
	 */
	public function revert_to_previous_revision($revision)
	{	
		// if its an id, convert to actual revision
		if (is_int($revision))
		{
			$revision = $this->get_revision($revision);
		}

		// Get previous revision
		$model = $this->revision_model;
		$previous_revision = $model::where($this->data_type_id.'_id','=',$this->id)->where('id','<',$revision->id)->take(1)->order_by('id','DESC')->get();

		// return false if no viable results are found to revert to
		if (sizeof($previous_revision) == 0)
		{
			return false;
		}

		// Switch revision to found result
		return $this->use_revision($previous_revision[0]);
	}

	/**
	 * use a particular revision as the new "current"
	 * 
	 * @param $revision Object|id
	 * @return $revision
	 */
	public function use_revision($revision)
	{
		// If its an id, convert to actual revision
		if(is_int($revision)){
			$revision = $this->get_revision($revision);
		}

		// Mark all later revisions as unused if we are reverting back
		$model = $this->revision_model;
		$model::where($this->data_type_id.'_id','=',$this->id)->where('id','>',$revision->id)->update(array('status'=>'unused'));

		// Remove the previously selected item if it is not "later" (and thus caught by the above code) and set to unused.
		$model::where($this->data_type_id.'_id','=',$this->id)->where('status','=','selected')->update(array('status'=>'draft'));
		
		// Get revision data and copy it back in to "current" object
		$revision_values = $revision->attributes;
		unset($revision_values['id']);
		unset($revision_values['created_at']);
		unset($revision_values['published_at']);
		unset($revision_values['edits_by']);
		unset($revision_values['made_live_by']);
		unset($revision_values['status']);
		unset($revision_values[strtolower($this->data_type_id).'_id']);

		if ($this->live != 0 && $revision->status != 'live') $this->live = 1;

		// Save this revision as the new current
		$this->fill($revision_values);
		parent::save();

		// Update revisions status to selected (assuming its not a live one)
		if ($revision->status != 'live')
		{
			$revision->status = 'selected';
			$revision->save();
		}

		return $revision;
	}

	/**
	 * Get list of attributes used in revisionable object
	 *
	 * @param year Year to return results for
	 * @return array of attributes
	 */
	public static function get_attributes_list($year = false)
	{
		$options = array();

		$model = get_called_class().'Field';

		if (!$year)
		{
			$data = $model::get();
		} 
		else 
		{
			$data = $model::where('year','=',$year)->get();
		}

		foreach ($data as $record) {$options[$record->colname] = $record->field_name;}

		return $options;
	}

	/**
	 * Removes the automatically generated field ids from our field names.
	 * 
	 * @param $record Record to remove field ids from.
	 * @return $new_record Record with field ids removed.
	 */
	public static function remove_ids_from_field_names($record)
	{
		$new_record = array();
		
		foreach ($record as $name => $value) 
		{
			$new_record[preg_replace('/_\d{1,3}$/', '', $name)] = $value;
		}

		return $new_record;
	}

	/**
	* Generate the feed index.json
	*
	* @param $programme thats been added
	* @param $path path to cache location
	*/
	private function generate_feed_index($new_programme, $path)
	{
		$index_file = $path.'index.json';

		// Obtain names for required fields
		$title_field = Programme::get_title_field();
		$slug_field = Programme::get_slug_field();
		$withdrawn_field = Programme::get_withdrawn_field();
		$suspended_field = Programme::get_suspended_field();
		$subject_to_approval_field = Programme::get_subject_to_approval_field();
		$new_programme_field = Programme::get_new_programme_field();
		$mode_of_study_field = Programme::get_mode_of_study_field();
		$ucas_code_field = Programme::get_ucas_code_field();
		$search_keywords_field = Programme::get_search_keywords_field();

		$index_data = array();

		// Query all data for the current year that includes both a published revison & isn't suspended/withdrawn
		$programmes = ProgrammeRevision::where('year','=',$new_programme->year)
						->where('status','=','live')
						->where($withdrawn_field,'!=','true')
						->where($suspended_field,'!=','true')
						->get();

		// Build array
		foreach($programmes as $programme)
		{
			$index_data[$programme->programme_id] = array(
				'id' => $programme->programme_id,
				'name' => $programme->$title_field,
				'slug' => $programme->$slug_field,
				'award' => ($programme->award != null) ? $programme->award->name : '',
				'subject' => ($programme->subject_area_1 != null) ? $programme->subject_area_1->name : '',
				'main_school' =>  ($programme->administrative_school != null) ? $programme->administrative_school->name : '',
				'secondary_school' =>  ($programme->additional_school != null) ? $programme->additional_school->name : '',
				'campus' =>  ($programme->location != null) ? $programme->location->name : '',
				'new_programme' => $programme->$new_programme_field,
				'subject_to_approval' => $programme->$subject_to_approval_field,
				'mode_of_study' => $programme->$mode_of_study_field,
				'ucas_code' => $programme->$ucas_code_field,
				'search_keywords' => $programme->$search_keywords_field,
			);
		}

		// Save as JSON
		file_put_contents($index_file, json_encode($index_data));
	}

	/**
	 * Generate all the necessary JSON files that are used in our API. These are:
	 * GlobalSettings.json
	 * ProgrammeSettings.json
	 * Index.json -- this function calls $this->generate_feed_index() to generate this
	 * {programme_id}.json
	 * 
	 * If we're saving a programme, we generate the programme's json file as well as update our index file to reflect the changes.
	 * All other revisionables are 
	 * 
	 * @param $revision The revision to base our saving on
	 */
	public function generate_feed_file($revision)
	{
		// global, settings, programme
		$data_type = get_called_class();
		$cache_location = path('storage') .'api'.'/ug/'.$revision->year.'/';

		// if our $cache_location isnt available, create it
		if (!is_dir($cache_location))
		{
			mkdir($cache_location, 0755, true);
		}

		// if we're saving a programme
		if($data_type == 'Programme')
		{
			file_put_contents($cache_location.$revision->programme_id.'.json', json_encode($revision->to_array()));
			$this->generate_feed_index($revision,$cache_location);
		}
		else
		{
			file_put_contents($cache_location.strtolower($data_type).'.json', json_encode($revision->to_array()));
		}
	}

}
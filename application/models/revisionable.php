<?php
/**
 * Revisionable.
 * 
 * A model for datatypes that need to maintain revisions of itself.
 */
class Revisionable extends SimpleData {

	// Revision model (name of model for revisions of this type)
	public static $revision_model = false;

	// Data Type (Programme, Global, etc)
	public $data_type = false;
	
	// Id used to link items of datatype (optional)
	protected $data_type_id = false;

	// Does this model seperate items by year? (false by default, although true for (all|most) revisionble types)
	public static $data_by_year = true;

	// The different live statuses
	public static $publish_statuses = array('new', 'editing', 'published');

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
		if(!static::$revision_model)  static::$revision_model = $this->data_type .'Revision';
		if(!$this->data_type_id) $this->data_type_id = strtolower($this->data_type);

		// Pass to real constructor
		parent::__construct($attributes, $exists);

		// Ensure default status is 0
		$this->live_revision = 0;
	}

	/**
	 * Save instance of this object along with a revision.
	 *
	 * @return true|false success
	 */
	public function save()
	{
		if (!$this->dirty()) return true;

		// Save self.
		$success = parent::save();

		// If instance_id  isn't set, set it to value of id.
		// null for new records, 0 for created ones.
		if ($this->instance_id === null || $this->instance_id  === 0){
			$this->instance_id = $this->id;
			$this->raw_save();
		}

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
		$revision_model = static::$revision_model;

		// Get new revision instance
		$revision = new $revision_model;

		// Get attributes
		$revision_values = $this->attributes;
		unset($revision_values['id']);
		unset($revision_values['created_by']);
		unset($revision_values['live']);
		unset($revision_values['locked_to']);
		unset($revision_values['current_revision']);
		unset($revision_values['live_revision']);
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

		// If we are on the command line, no user will be logged in, use a dummy instead.
		// This will be used mostly when seeding the database.
		if (! Request::cli())
		{
			$revision_values['edits_by'] = Auth::user()->username;
		}
		else 
		{
			$revision_values['edits_by'] = 'seed';
		}

		// Status = selected
		$revision_values['status'] = 'selected';

		// Set Programme ID so we know which programme this revision belongs to.
		$revision_values[$this->data_type_id.'_id'] = $this->id;

		// Set the data in to the revision
		$revision->fill($revision_values);

		// Save everything
		$success = $revision->save();

		// Set previously active revision back to draft (if its not live)
		if(isset($this->attributes['current_revision']) && $this->attributes['current_revision'] != $this->attributes['live_revision']){
			$active = $this->get_active_revision();
			$active->status = 'draft';
			$active->save();
		} 

		// link new revision
		$this->current_revision = $revision->id;
		$this->raw_save();

		// Save revision
		return $success;
	}

	/**
	 * Get all revisions for this item (or revisions in a particular status if status is passed)
	 * @param $status status of revisions to return
	 * @return array of revisions
	 */
	public function get_revisions($status = false)
	{
		$model = static::$revision_model;
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
		// Get revision
		$model = static::$revision_model;
		$revision = $model::find($revision_id);

		if(empty($revision)) {
			throw new RevisioningException("Revision does not exist.");
		}

		// Ensure revision belongs to this item or throw an exception.

		// We don't know what the column of the database that relates a revision to what it is a revision of.
		// For example a revision of a programme has revisions with a column called programme_id that express this relationship
		// We need to use $this->data_type_id to work this out.
		$data_type_key = $this->data_type_id . '_id';

		if ($revision->$data_type_key == $this->id)
		{
			return $revision;
		}
		else
		{
			throw new RevisioningException("Revision does not belong to this object.");
		}
	}

	/**
	 * Get currently active revision.
	 * 
	 * @param $columns columns to return
	 * @return active revision instance
	 */
	public function get_active_revision($columns = array('*'))
	{
		$model = static::$revision_model;
		return $model::where('id', '=', $this->current_revision)->first($columns);
	}	

	/**
	 * Get published/live revision
	 *
	 * @param $columns columns to return
	 * @return live revision instance
	 */
	public function find_live_revision($columns = array('*'))
	{	
		$model = static::$revision_model;
		return $model::where('id', '=', $this->live_revision)->first($columns);
	}

	/**
	 * Get publish status
	 *
	 * @return publish status
	 */
	public function get_publish_status()
	{
		// return new
		if($this->live_revision == 0){
			return static::$publish_statuses[0];
		}

		// return published
		elseif($this->live_revision == $this->current_revision){
			return static::$publish_statuses[2];
		}

		// return editing
		else{
			return static::$publish_statuses[1];
		}
	}

	/**
	 * Roll over to year
	 *
	 * @param year to roll over to.
	 * @return success true|false
	 */
	public function roll_over_to_year($year)
	{	
		$model = $this->data_type;

		// Check is not already rolled over
		if($model::where("year", "=", $year)->where("instance_id", "=", $this->instance_id)->first(array('id')) != null){
			// Fail since this has already been rolled over
			return false;
		}
		// Get basic values to rollover
		$rollover_values = $this->attributes;

		// remove values we dont want to keep
		foreach(array('id','live',) as $key){
			unset($rollover_values[$key]);
		} 

		// Change year
		$rollover_values->year = $year;

		// Create new instance and set values
		$new = new $model;
		$new->fill($rollover_values);
		
		// Save to create revisions
		return $new->save();
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

		//	If revision being made live us current, set item status to say there are no later versions
		if($revision->status == 'selected'){
			// Unlock revision as it no longer contains any changes
			if(isset($this->attributes['locked_to'])) $this->locked_to = '';
		}

		//Update live revision pointer
		$this->live_revision = $revision->id;

		parent::save();
		
		// Set previous live to a non-live status (prior_live so we can tell them apart from drafts that have never been used)
		$revision_model = static::$revision_model;
		$revision_model::where($this->data_type_id.'_id','=',$this->id)->where('status','=','live')->update(array('status'=>'prior_live'));

		// Hacky, but better than doing an if type == programme.
		if(isset($revision->attributes['under_review'])){
			// Remove under review state for any programmes of this type, prior to the published revision.
			// Since the latest copy will include the changes of the "under-review" item, by pushing a revision live
			// a user is implicty accepting the former changes.
			$revision_model::where('under_review', '=', 1)->where($this->data_type_id.'_id','=',$this->id)->where('id','<=',$revision->id)->update(array('under_review'=>0));
		}

		// Update and save this revision 
		$revision->status = 'live';
		$revision->published_at = date('Y-m-d H:i:s');
		$revision->made_live_by = Auth::user()->username;
		$revision->save();
		// Update feed file & kill output caches
		static::generate_api_data($revision->year, $revision);
		API::purge_output_cache();

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
		$model = static::$revision_model;
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
		$model = static::$revision_model;
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
		unset($revision_values['under_review']);
		unset($revision_values[strtolower($this->data_type_id).'_id']);

		// update current revisions pointer
		$this->current_revision = $revision->id;

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
	 * get API Data
	 * Return cached data from data type
	 *
	 * @param year Year to get data for
	 * @return data Object
	 */
	public static function get_api_data($year = false){

		// generate keys
		$model = strtolower(get_called_class());
		$cache_key = 'api-'.$model.'-'.$year;

		// Get data from cache (or generate it)
		return (Cache::has($cache_key)) ? Cache::get($cache_key) : static::generate_api_data($year);

	}

	/**
	 * generate API data
	 * Get live version of API data from database
	 *
	 * @param year Year to get data for
	 * @return $revision Object
	 */
	public static function generate_api_data($year = false, $revision = false){

		// Get model and key
		$model = strtolower(get_called_class());
		$cache_key = 'api-'.$model.'-'.$year;

		// If revision not passed, get data
		if(!$revision){
			$revisionModel = $model::$revision_model;
			$revision = $revisionModel::where('status', '=', 'live')->where('year', '=', $year)->first();
		} 

		// Return false if there is no live revision
		if(sizeof($revision) === 0 || $revision === null){
			return false;
		} 

		// Store data in to cache
		Cache::put($cache_key, $revision_data = $revision->attributes, 2628000);
		// return
		return $revision_data;
	}

	/**
     * Simplifies this object by removing IDs from its field names.
     * 
     * @return StdClass A simplified version of the object minus its field names.
     */
    public function trim_ids_from_field_names()
    {
    	$trimmed = new StdClass();

    	foreach ($this->attributes as $name => $value) 
		{
			$name = static::trim_id_from_field_name($name);
			$trimmed->$name = $value;
		}

		return $trimmed;
    }

    /**
     * strips the ID from a field name
     * 
     * @param $name the field name
     * @return string a field name without an id.
     */
    public static function trim_id_from_field_name($name)
    {
		return preg_replace('/_\d{1,4}$/', '', $name);
    }

    // Fields cache
    public static $fields = array();

    // Load field mapping - true field name => field colname
	public static function load_field_map(){

		$model = get_called_class().'Field';

		$field_map = array();
		$field_list = $model::get(array('colname'));

		foreach($field_list as $field){
			$field_map[static::trim_id_from_field_name($field->colname)] = $field->colname;
		}
		static::$fields = $field_map;
	}

	// Add additional check to call magic method
	public function __call($method, $parameters)
	{	
		// If matchs get_X_field
		if (ends_with($method, '_field') && starts_with($method, 'get_')){
			// Check we have a fields map, if not load it
			if( sizeof(static::$fields) === 0 ) static::load_field_map();
			// get just X (remove get_ and _field)
			$method = str_replace(array('get_','_field'),'',$method);
			// return result | null
			return isset(static::$fields[$method]) ? static::$fields[$method] : null;
		}
		// Else, normal action
		return parent::__call($method, $parameters);
	}

	public static function all_active($sort_by='')
	{
		if ($sort_by != '')
		{
			return static::order_by($sort_by, 'asc');
		}
		return static::where(true, '=', true);
	}



}

class RevisioningException extends \Exception {}
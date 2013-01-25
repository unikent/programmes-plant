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
		if(!static::$revision_model)  static::$revision_model = $this->data_type .'Revision';
		if(!$this->data_type_id) $this->data_type_id = strtolower($this->data_type);

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

		// If data_type_id isn't set, set it to value of id.
		// null for new records, 0 for created ones.
		$ref_id = $this->data_type_id.'_id';
		if ($this->{$ref_id} === null || $this->{$ref_id} === 0){
			$this->{$ref_id} = $this->id;
			parent::raw_save();
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
			$revision_values['edits_by'] = Auth::user();
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

		// Ensure revision belongs to this item
		$data_type_key = $this->data_type_id.'_id';	
		if($revision->$data_type_key == $this->id){
			return $revision;
		}else{
			// Exception ifn not.
			throw new RevisioningException("Revision does not belong to this object.");
		}
	}

	/**
	 * Get currently active revision
	 * @return active revision instance
	 */
	public function get_active_revision($columns = array('*'))
	{
		// If all is up to date (live=2) return live/selected item
		// else get item marked as selected
		$model = static::$revision_model;
		if ($this->live == 2)
		{
			return $model::where($this->data_type_id.'_id','=',$this->id)->where('year','=',$this->year)->where('status','=','live')->first($columns);
		}
		else
		{
			return $model::where($this->data_type_id.'_id','=',$this->id)->where('year','=',$this->year)->where('status','=','selected')->first($columns);
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
		$revision_model = static::$revision_model;
		$revision_model::where($this->data_type_id.'_id','=',$this->id)->where('status','=','live')->update(array('status'=>'prior_live'));

		// Update and save this revision 
		$revision->status = 'live';
		$revision->published_at = date('Y-m-d H:i:s');
		$revision->made_live_by = Auth::user();
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
		$model = get_called_class();
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
		Cache::put($cache_key, $revision_data = $revision->to_array(), 2628000);
		// return
		return $revision_data;
	}

	/**
     * Simplifies the object by IDs from field names.
     * 
     * @todo This duplicates functionality in revisionable. Refactor or remove.
     * @return StdClass A simplified version of the object minus its field names.
     */
    public function trim_ids_from_field_names()
    {
    	$trimmed = new StdClass();

    	foreach ($this->attributes as $name => $value) 
		{
			$name = preg_replace('/_\d{1,3}$/', '', $name);
			$trimmed->$name = $value;
		}

		return $trimmed;
    }

}

class RevisioningException extends \Exception {}
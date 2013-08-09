<?php

use Laravel\CLI\Command;

abstract class Programme extends Revisionable {

	protected $data_type_id = 'programme';

	public static function get_title_field(){
		return static::get_programme_title_field();
	}
	/**
	 * Get this programme's award.
	 * 
	 * @return Award The award for this programme.
	 */
	public function award()
	{
		$type = URLParams::get_type();
	  	return $this->belongs_to($type.'_Award', static::get_award_field());
	}

	/**
	 * Get this programme's first subject area.
	 * 
	 * @return Subject The first subject area for this programme.
	 */
	public function subject_area_1()
	{
		$type = URLParams::get_type();
		return $this->belongs_to($type.'_Subject', static::get_subject_area_1_field());
	}

	/**
	 * Get this programme's administrative school.
	 * 
	 * @return School The administrative school for this programme.
	 */
	public function administrative_school()
	{
	  return $this->belongs_to('School', static::get_administrative_school_field());
	}

	/**
	 * Get this programme's additional school.
	 * 
	 * @return School The additional school for this programme.
	 */
	public function additional_school()
	{
	  return $this->belongs_to('School', static::get_additional_school_field());
	}

	/**
	 * Get this programme's campus.
	 * 
	 * @return School The location for this programme.
	 */
	public function location()
	{
	  return $this->belongs_to('Campus', static::get_location_field());
	}

	/**
	 * Save changes to programme
	 * 
	 * @return true|false
	 */
	public function save()
	{
		if (!$this->dirty()) return true;

		// If user has approve powers, auto accept
		if(Auth::user()->can('approve_revisions'))
		{
			return parent::save();
		}
		else
		{
			// Set to be in draft (locked, but not enforced)
			$this->locked_to = Auth::user()->username;
			return parent::save();
		}

	}


	/**
	 * Update the current revision with changes
	 */
	public function update_current_revision(){
		// Sync changes to revision
		$revision = $this->get_active_revision();
		foreach($this->get_dirty() as $col => $value){
			$revision->{$col} = $value;
		}
		// Save revision & current
		$revision->save();
		$this->raw_save();	
	}


	/**
	 * Submits a revision into the inbox of EMS for editing, setting the status to 'under_review'.
	 * This should work for all revisionable types that inherit from this.
	 * Presently only the revisions of programmes are surfaced.
	 * 
	 * @param int|Revision  Revision object or integer to send for editing.
	 */
	public function submit_revision_for_editing($revision)
	{
		if (! is_numeric($revision) and ! is_object($revision))
		{
			throw new RevisioningException('submit_revision_for_editing only accepts revision objects or integers as parameters.');
		}

		// If we got an ID, then convert it to a revision.
		if (is_numeric($revision))
		{
			$revision = $this->get_revision($revision);
		}
		$revision_model = static::$revision_model;
		// Remove review status from previous revisions
		$revision_model::where('under_review', '=', 1)->where('programme_id', '=', $revision->programme_id)->update(array('under_review'=>0));

		// Set this revision to be under review
		$revision->under_review = 1;
		$revision->save();
	}



	/**
	 * Gets all programme revisions that are currently under review.
	 * 
	 * @return array $under_review  An array of programme revisions currently under review.
	 */
	public static function get_under_review()
	{
		$revision_model = static::$revision_model;
		return $revision_model::where('under_review', '=', 1)->order_by('updated_at', 'asc')->get();
	}

	/**
	 * This function replaces the passed-in ids with their actual record
	 * Limiting the record to its name and id
	 *
     * @param $ids List of ids to lookup
	 * @param $year Year course should be returned from.
	 * @return array of objects matching id's
	 */
	public static function replace_ids_with_values($ids, $year = false, $titles_only = false)
	{

		// If nothing is set, return an empty array
		if(trim($ids) == '') return array();
		// Get list of ids to swap out & grab api data from cache
		$id_array = explode(',', $ids);
		$cached_data = static::get_api_index($year);
		// Create new array of actual values matching the ids from the cache
		$values = array();
		foreach ($id_array as $id) 
		{
			// Only display relation IF programme is published
			if(isset($cached_data[$id])){
				if($titles_only){
					$values[] = $cached_data[$id]['name'];
				}else{
					$values[] = $cached_data[$id];
				}
			}
		}

		return $values;
	}

	/**
	 * Generate fresh copy of data, triggered on save.
	 *
	 * @param $year year data is for
	 * @param $revision data set saved with
	 */
	public static function generate_api_data($year = false, $revision = false, $type = 'ug')
	{
		// Regenerate data to store in caches
		static::generate_api_programme($revision->instance_id, $year, $revision);
		static::generate_api_index($year);
		
		// regenerate the module data from webservices as the revision is made live
		// we don't want to do this in a test or local environment
		if ( Request::env() != 'test' && Request::env() != 'local' )
		{
			Command::run(array('moduledata:modules', $revision, $year, URLParams::get_type(), false));
		}
	}

	/**
	 * get a copy of a programme (from cache if possible)
	 *
	 * @param id of programme
	 * @param year  of programme
	 * @return programmes index
	 */
	public static function get_api_programme($iid, $year)
	{
		$tbl = static::$table;
		$cache_key = "api-{$tbl}-$year-{$iid}";
		return (Cache::has($cache_key)) ? Cache::get($cache_key) : static::generate_api_programme($iid, $year);
	}

	/**
	 * generate copy of programme data from live DB
	 *
	 * @param iid (instance id) of programme
	 * @param year  of programme
	 * @param revsion data - store this to save reloading dbs when generating
	 * @return programmes index
	 */
	public static function generate_api_programme($iid, $year, $revision = false)
	{

		$tbl = static::$table;
		$cache_key = "api-{$tbl}-$year-{$iid}";

		$revision_model = static::$revision_model;

		// If revision not passed, get data
		if(!$revision){

			$p = static::where('instance_id', '=', $iid)->where('year', '=', $year)->first();
			$revision = ($p !== null) ? $p->find_live_revision() : null;
		}

		// Return false if there is no live revision
		if(sizeof($revision) === 0 || $revision === null){
			return false;
		} 

		Cache::put($cache_key, $revision_data = $revision->attributes, 2628000);

		// return
		return $revision_data;
	}

	/**
	 * get a copy of the programmes listing data (from cache if possible)
	 *
	 * @param $year year to get index for
	 * @return programmes index
	 */
	public static function get_api_index($year)
	{	
		$type = static::$type;
		$cache_key = "api-index-{$type}.index-$year";
		return (Cache::has($cache_key)) ? Cache::get($cache_key) : static::generate_api_index($year);
	}

	/**
	 * get a copy of the subjects mapping data (from cache if possible)
	 *
	 * @param $year year to get index for
	 * @return programmes mapping
	 */
	public static function get_api_related_programmes_map($year)
	{	
		$type = static::$type;
		$cache_key = "api-index-{$type}.api-programmes-$year-subject-relations";

		if(Cache::has($cache_key)){
			return Cache::get($cache_key);
		}else{
			// Generate cache (this returns index, not mappings)
			static::generate_api_index($year);
			// get cache
			return  Cache::get($cache_key);
		}
	}

	/**
	 * Forget all index's & relation maps
	 * @todo find memory version of purge
	 */
	public static function forget_api_index(){
		// @todo work out a way of purging this data in tests
		// so we can test logic creates/removes the correct files
		if(Request::env() != 'test'){
			// Pokémon expection handling, gotta catch em all.
			try {
				$type = static::$type;
				Cache::purge("api-index-{$type}");
			}catch (Exception $e) {
				// Do nothing, all this means if there was no directory (yet) to wipe
			}
		}
	}

	/**
	 * generate new copy of programmes listing data from live DB
	 *
	 * @param $year year to get index for
	 * @return programmes index
	 */
	public static function generate_api_index($year)
	{
		$type = static::$type;

		$revision_model = static::$revision_model;

		$subject_cat_model = $type.'_SubjectCategory';

		// Set cache keys
		
		$cache_key_index = "api-index-{$type}.index-$year";
		$cache_key_subject = "api-index-{$type}.api-programmes-$year-subject-relations";

		// Obtain names for required fields
		$title_field = static::get_title_field();
		$slug_field = static::get_slug_field();
		$subject_categories_field = static::get_subject_categories_field();
		
		$subject_to_approval_field = static::get_subject_to_approval_field();
		$new_programme_field = static::get_new_programme_field();
		$mode_of_study_field = static::get_mode_of_study_field();
		$ucas_code_field = static::get_ucas_code_field();
		$search_keywords_field = static::get_search_keywords_field();
		$pos_code_field = static::get_pos_code_field();
		$awarding_institute_or_body_field = static::get_awarding_institute_or_body_field();
		$module_session_field = static::get_module_session_field();
		
		$award_field = static::get_award_field();
		$subject_area_1_field = static::get_subject_area_1_field();
		$subject_area_2_field = static::get_subject_area_2_field();
		$location_field = static::get_location_field();
		$additional_locations_field = static::get_additional_locations_field();
		$administrative_school_field = static::get_administrative_school_field();
		$additional_school_field = static::get_additional_school_field();

		$withdrawn_field = static::get_programme_withdrawn_field();
		$suspended_field = static::get_programme_suspended_field();

		$programme_type_field = static::get_programme_type_field();

		$index_data = array();


		$fields = array(
					'instance_id',
					 $title_field,
					 $slug_field,
					 $award_field,
					 $subject_area_1_field,
					 $subject_categories_field,
					 $administrative_school_field,
					 $additional_school_field,
					 $location_field,
					 $new_programme_field,
					 $subject_to_approval_field,
					 $mode_of_study_field,
					 $search_keywords_field,
					 $pos_code_field,
					 $awarding_institute_or_body_field,
					 $module_session_field,
					 $subject_area_2_field,
					 $programme_type_field
		);
		// If UG, add ucas field
		if ($type == 'ug') {
			$fields[] = $ucas_code_field;
		}
		// if pg add additional locations field
		if ($type == 'pg') {
			$fields[] = $additional_locations_field;
		}

		// Query all data for the current year that includes both a published revison & isn't suspended/withdrawn
		// @todo Use "with" to lazy load all related fields & speed this up a bit.
		$programmes = $revision_model::with(array('award', 'subject_area_1', 'administrative_school', 'additional_school', 'location'))->where('year','=', $year)
						->where('status','=','live')
						->where($withdrawn_field,'!=','true')
						->where($suspended_field,'!=','true')
						->get($fields);

		

		// Build index array
		foreach($programmes as $programme)
		{
			// Get direct access data stores
			$attributes = $programme->attributes;
			$relationships = $programme->relationships;

			if($type == 'pg')
			{
				$awards = PG_Award::replace_ids_with_values($programme->$award_field, false, true);
				$awards = implode(', ', $awards);

				$additional_locations = Campus::replace_ids_with_values($programme->$additional_locations_field, false, true);
				unset($additional_locations[0]);
				$additional_locations = implode(', ', $additional_locations);
				$additional_locations = preg_replace("/, ([^,]+)$/", " and $1", $additional_locations);
			}
			else
			{
				$awards = isset($relationships["award"]) ? $relationships["award"]->attributes["name"] : '';
				$additional_locations = '';
			}

			$index_data[$attributes['instance_id']] = array(
				'id' 		=> 		$attributes['instance_id'],
				'name' 		=> 		$attributes[$title_field],
				'slug' 		=> 		$attributes[$slug_field],
				'award' 	=> 		$awards,
				'subject'	 => 	isset($relationships["subject_area_1"]) ? $relationships["subject_area_1"]->attributes["name"] : '',
				'subject_categories' => isset($attributes[$subject_categories_field]) ? $subject_cat_model::replace_ids_with_values($attributes[$subject_categories_field], false, true) : '',
				'main_school' =>  isset($relationships["administrative_school"]) ? $relationships["administrative_school"]->attributes["name"] : '',
				'secondary_school' =>  isset($relationships["additional_school"]) ? $relationships["additional_school"]->attributes["name"] : '',
				'campus' 	=>  isset($relationships["location"]) ? $relationships["location"]->attributes["name"] : '',
				'additional_locations' => $additional_locations,
				'new_programme' => 	$attributes[$new_programme_field],
				'subject_to_approval' => $attributes[$subject_to_approval_field],
				'mode_of_study' => 	$attributes[$mode_of_study_field],
				'ucas_code' 	=> 	isset($attributes[$ucas_code_field]) ? $attributes[$ucas_code_field] : '',
				'search_keywords' => $attributes[$search_keywords_field],
				'campus_id' => isset($relationships["location"]) ? $relationships["location"]->attributes["identifier"] : '',
				'pos_code' => $attributes[$pos_code_field],
				'awarding_institute_or_body' => $attributes[$awarding_institute_or_body_field],
				'module_session' => isset($attributes[$module_session_field]) ? $attributes[$module_session_field] : '',
				'subject2'	 => 	isset($relationships["subject_area_2"]) ? $relationships["subject_area_2"]->attributes["name"] : '',
				'programme_type' => isset($attributes[$programme_type_field]) ? $attributes[$programme_type_field] : ''
			);
		}

		// Store index data in to cache
		Cache::put($cache_key_index, $index_data, 2628000);

		// Map relaated subjects.
		$subject_relations = array();

		// For each programme in output
		foreach($programmes as $programme){

			$subject_area_1 = $programme->attributes[$subject_area_1_field];
			$subject_area_2 = $programme->attributes[$subject_area_2_field];
			$instance_id 	= $programme->attributes["instance_id"];
			// Create arrays as needed.
			if(!isset($subject_relations[$subject_area_1])) $subject_relations[$subject_area_1] = array();
			if(!isset($subject_relations[$subject_area_2])) $subject_relations[$subject_area_2] = array();
			// Add this programme to subject
			$subject_relations[$subject_area_1][$instance_id] = $index_data[$instance_id];
			// If second subject isn't the same, add it to that also
			if($subject_area_1 != $subject_area_2){
				$subject_relations[$subject_area_2][$instance_id] = $index_data[$instance_id];
			}
		}

		// Store subject mapping data in to cache
		Cache::put($cache_key_subject, $subject_relations, 2628000);

		// return
		return $index_data;
	}
	
	
	/**
	 * unpublishes a given revision
	 * 
	 * @param $revision Object|id
	 * @return $revision
	 */
	public function unpublish_revision($revision)
	{
		
		// Get the currently "active/selected" revision.
		// If this revision is currently live, change its status to selected (so the system knows which revision is being edited/used)
		// If the active revision is already current, leave it be
		$active_revision = $this->get_active_revision();
		if ($active_revision->status == 'live')
		{
			$active_revision->status = 'selected';
			$active_revision->save();
		}
		else
		{
			$revision->status = 'prior_live';
			$revision->save();
		}
		
		$this->live = 0;
		$this->live_revision = 0;
		parent::save();
		
		// Update feed file & kill output caches
		static::generate_api_index($revision->year);
		API::purge_output_cache();
		Cache::forget('api-'. static::$table . '-'. $revision->year . '-'. $revision->instance_id);
		
		return $revision;
	}

	/**
	 * Delete this programme by hiding it and unpublishing any of its published revisions
	 * 
	 * @param $revision Object|id
	 * @return $revision
	 */
	public function delete()
	{
		// SimpleData already hides this
		parent::delete();

		// Now unpublish any live revisions
		$revision_model = static::$revision_model;
		// Use programme id as is linked to revisions for this year directly
		$live_revision = $revision_model::where('programme_id', '=', $this->id)->where('status', '=', 'live')->get();
		if(count($live_revision) > 0)
		{
			foreach ($live_revision as $revision) {
				$this->unpublish_revision($revision);
			}
		}
	}

	/**
	 * Find related programmes using API. Returns array containing any course in the given year that is in either subject_1 or subject_2.
	 * 
	 * @param $subject_1 is course part of subject 1
	 * @param $subject_2 is course part of subject 2
	 * @param $year is course in year
	 * @param $self_id Id of record this is called from (So programmes are not related to themselves)
	 * @return array of realted programmes
	 */
	public static function get_programmes_in($subject_1, $subject_2, $year, $self_id = false)
	{
		$mapping = static::get_api_related_programmes_map($year);

		// If subject isn't set, just return an empty array of relations.
		if($subject_1 == null){
			return array();
		} 
		// If subject 2 is null, assume is duplicate of 1.
		if($subject_2 == null){
			$subject_2 = $subject_1;
		} 

		// Get all related programmes.
		if($subject_1 != $subject_2){
			$related_courses_array = array_merge($mapping[$subject_1], $mapping[$subject_2]);
		}else{
			// Empty array if subject 1 is empty
			$related_courses_array = isset($mapping[$subject_1]) ? $mapping[$subject_1] : array();
		}

		// Remove self from list as theres no point it being related to itself
		if($self_id) unset($related_courses_array[$self_id]);
		 
		return $related_courses_array;
	}

	/**
	 * Generate data to display diff of two revisions
	 * 
	 * @param $revision_1 - previous revision
	 * @param $revision_2 - new revision
	 * @return array(revision_1, revision_2, attributes)
	 */
	public static function revision_diff($revision_1, $revision_2){

		// Revisions are blank, return false
		if($revision_1==null) return false;

		// Get programme data
		$attribute_names = static::get_attributes_list();

		$field_model = static::$type.'_ProgrammeField';
		$attribute_types = $field_model::get_api_data();

		// init attributes array
		$attributes = array();
		foreach(array_keys($revision_1->attributes) as $attribute){

			// Ignore these columns
			if(in_array($attribute, array('id', 'programme_id', 'under_review', 'status', 'created_by', 'published_by', 'created_at', 'updated_at', 'hidden', 'edits_by', 'published_at', 'made_live_by', 'instance_id'))) continue;

			// Add attribute to "attributes" array with label and "attribute_id"
			$attributes[] = array(
				'attribute' => $attribute,
				'label' => isset($attribute_names[$attribute]) ? $attribute_names[$attribute] : (string) __('programmes.' . $attribute),
			);

			// If an attribute has a relation (is in the $attribute_types array)
			// use the type to lookup & swap in the "text" equivelents to the ids before comparing
			if(array_key_exists($attribute, $attribute_types)){
				$type = $attribute_types[$attribute];

				$revision_1->{$attribute} = implode(',', $type::replace_ids_with_values($revision_1->{$attribute} , $revision_1->attributes['year'], true) );
				
				if($revision_2 != null){
					$revision_2->{$attribute} =  implode(',', $type::replace_ids_with_values($revision_2->{$attribute} , $revision_2->attributes['year'], true) );
				}
					
			}	

			// Apply diff highlighting to "revision_2" for this attribute
			if($revision_2 != null){
				$revision_2->{$attribute} = SimpleDiff::htmlDiff($revision_1->{$attribute}, $revision_2->{$attribute});
			}
			
			
		}

		// Return required data
		return array(
			'revision_1' => $revision_1,
			'revision_2' => ($revision_2 != null) ? $revision_2 : null,
			'attributes' => $attributes,
 		);

	}


}

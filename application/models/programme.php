<?php

class Programme extends Revisionable {

	public static $table = 'programmes';
	
	public static $revision_model = 'ProgrammeRevision';

	/**
	 * Get the name of the title field/column in the database.
	 * 
	 * @return The name of the title field.
	 */
	public static function get_title_field()
	{
		return 'programme_title_1';
	}

	/**
	 * Get the name of the slug field/column in the database.
	 * 
	 * @return The name of the slug field.
	 */
	public static function get_slug_field()
	{
		return 'slug_2';
	}

	/**
	 * Get the name of the subject area 1 field/column in the database.
	 * 
	 * @return The name of the subject area 1 field.
	 */
	public static function get_subject_area_1_field()
	{
		return 'subject_area_1_8';
	}

	/**
	 * Get the name of the award field/column in the database.
	 * 
	 * @return The name of the award field.
	 */
	public static function get_award_field()
	{
		return 'award_3';
	}
	
	/**
	 * Get the name of the 'programme withdrawn' field/column in the database.
	 * 
	 * @return The name of 'programme withdrawn the  field.
	 */
	public static function get_withdrawn_field()
	{
		return 'programme_withdrawn_54';
	}
	
	/**
	 * Get the name of the 'programme suspended' field/column in the database.
	 * 
	 * @return The name of the 'programme suspended' field.
	 */
	public static function get_suspended_field()
	{
		return 'programme_suspended_53';
	}
	
	/**
	 * Get the name of the 'subject to approval' field/column in the database.
	 * 
	 * @return The name of the 'subject to approval' field.
	 */
	public static function get_subject_to_approval_field()
	{
		return 'subject_to_approval_52';
	}
	
	/**
	 * Get the name of the 'get new programme' field/column in the database.
	 * 
	 * @return The name of the 'get new programme' field.
	 */
	public static function get_new_programme_field()
	{
		return 'new_programme_50';
	}
	
	/**
	 * Get the name of the 'mode of stude' field/column in the database.
	 * 
	 * @return The name of the 'mode of study' field.
	 */
	public static function get_mode_of_study_field()
	{
		return 'mode_of_study_12';
	}
	
	/**
	 * Get the name of the 'ucas code' field/column in the database.
	 * 
	 * @return The name of the 'ucas code' field.
	 */
	public static function get_ucas_code_field()
	{
		return 'ucas_code_10';
	}

	/**
	 * Get the name of the 'additional school' field/column in the database.
	 * 
	 * @return The name of the 'additional school' field.
	 */
	public static function get_additional_school_field()
	{
		return 'additional_school_7';
	}

	/**
	 * Get the name of the 'administrative school' field/column in the database.
	 * 
	 * @return The name of the 'administrative school' field.
	 */
	public static function get_administrative_school_field()
	{
		return 'administrative_school_6';
	}

	/**
	 * Get the name of the 'location' field/column in the database.
	 * 
	 * @return The name of the 'location' field.
	 */
	public static function get_location_field()
	{
		return 'location_11';
	}

	/**
	 * Get the name of the 'search_keywords' field/column in the database.
	 * 
	 * @return The name of the 'search_keywords' field.
	 */
	public static function get_search_keywords_field()
	{
		return 'search_keywords_46';
	}
	
	/**
	 * Get the name of the 'pos_code' field/column in the database.
	 * 
	 * @return The name of the 'pos_code' field.
	 */
	public static function get_pos_code_field()
	{
		return 'pos_code_44';
	}


	/**
	 * Get this programme's award.
	 * 
	 * @return Award The award for this programme.
	 */
	public function award()
	{
	  return $this->belongs_to('Award', static::get_award_field());
	}

	/**
	 * Get this programme's first subject area.
	 * 
	 * @return Subject The first subject area for this programme.
	 */
	public function subject_area_1()
	{
	  return $this->belongs_to('Subject', static::get_subject_area_1_field());
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
	 * @return School The additional school for this programme.
	 */
	public function location()
	{
	  return $this->belongs_to('Campus', static::get_location_field());
	}

	/**
	 * This function replaces the passed-in ids with their actual record
	 * Limiting the record to its name and id
	 *
     * @param $ids List of ids to lookup
	 * @param $year Year course should be returned from.
	 * @return array of objects matching id's
	 */
	public static function replace_ids_with_values($ids, $year = false)
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
			if(isset($cached_data[$id])) $values[] = $cached_data[$id];
		}

		return $values;
	}

	/**
	 * Creates a flat representation of the object for use in XCRI.
	 * 
	 * @return StdClass A flattened and simplified XCRI ready representation of this object.
	 */
	public function xcrify()
	{
		$clean_programme = $this->trim_ids_from_field_names();
		$clean_programme->url = 'http://www.kent.ac.uk/courses/undergraduate/' . $this->id . '/' . $this->slug;

		// Pull in award as eager loading doesn't appear to be working.
		$clean_programme->award = Award::find($this->award_3);

		// Set campus as default while we are making a dummy.
		$clean_programme->attendance_mode_id = 'CM';
		$clean_programme->attendance_mode = 'Campus';

		// Also dummy attendence_pattern_id for now. Set to daytime.
		$clean_programme->attendance_pattern = 'Daytime';
		$clean_programme->attendance_pattern_id = 'DT';

		$clean_programme->campus = Campus::find($this->location_11);

		// Deal with subjects in an ugly way.
		$clean_programme->subjects = array();
		$clean_programme->subjects[] = $this->jacs_code_subject_1_42;
		$clean_programme->subjects[] = $this->jacs_code_subject_2_43;

		$subject  = Subject::find($this->subject_area_1_8);
		$clean_programme->subjects[] = $subject->name;

		$subject  = Subject::find($this->subject_area_2_9);
		$clean_programme->subjects[] = $subject->name;

		// Leave as is for the moment.
		$clean_programme->cost = $this->tuition_fees;

		return $clean_programme;
	}

	/**
	 * Generate fresh copy of data, triggered on save.
	 *
	 * @param $year year data is for
	 * @param $revision data set saved with
	 */
	public static function generate_api_data($year = false, $revision = false)
	{
		// Regenerate data to store in caches
		static::generate_api_programme($revision->instance_id, $year, $revision);
		static::generate_api_index($year);
	}

	/**
	 * get a copy of a programme (from cache if possible)
	 *
	 * @param id of programme
	 * @param year  of programme
	 * @return programmes index
	 */
	public static function get_api_programme($id, $year)
	{
		$cache_key = "api-programme-ug-$year-$id";
		return (Cache::has($cache_key)) ? Cache::get($cache_key) : static::generate_api_programme($id, $year);
	}

	/**
	 * generate copy of programme data from live DB
	 *
	 * @param id of programme
	 * @param year  of programme
	 * @param revsion data - store this to save reloading dbs when generating
	 * @return programmes index
	 */
	public static function generate_api_programme($id, $year, $revision = false)
	{
		$cache_key = "api-programme-ug-$year-$id";

		$model = get_called_class();

		// If revision not passed, get data
		if(!$revision){
			$revision = ProgrammeRevision::where('instance_id', '=', $id)->where('year', '=', $year)->where('status', '=', 'live')->first();
		}

		// Return false if there is no live revision
		if(sizeof($revision) === 0 || $revision === null){
			return false;
		} 

		Cache::put($cache_key, $revision_data = $revision->to_array(), 2628000);

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
		$cache_key = "api-index.index-$year";
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
		$cache_key = "api-index.api-programmes-$year-subject-relations";

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
				Cache::purge("api-index");
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
		// Set cache keys
		$cache_key_index = "api-index.index-$year";
		$cache_key_subject = "api-index.api-programmes-$year-subject-relations";

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
		$pos_code_field = Programme::get_pos_code_field();
		
		$index_data = array();

		// Query all data for the current year that includes both a published revison & isn't suspended/withdrawn
		// @todo Use "with" to lazy load all related fields & speed this up a bit.
		$programmes = ProgrammeRevision::with(array('award','subject_area_1','administrative_school','additional_school','location'))->where('year','=', $year)
						->where('status','=','live')
						->where($withdrawn_field,'!=','true')
						->where($suspended_field,'!=','true')
						->get();

		// Build index array
		foreach($programmes as $programme)
		{
			$index_data[$programme->instance_id] = array(
				'id' => $programme->instance_id,
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
				'campus_id' => ($programme->location != null) ? $programme->location->identifier : '',
				'pos_code' => ($programme->$pos_code_field != null) ? $programme->$pos_code_field : '',
			);
		}

		// update times last generated
		$last_generated = ApiIndexTime::where('year', '=', $year)->where('level', '=', 'ug')->first();
		
		if($last_generated == null)
		{
			ApiIndexTime::create(array('year' => $year, 'level' => 'ug'))->save();
		}
		else
		{
			$last_generated->touch();
		}

		// Store index data in to cache
		Cache::put($cache_key_index , $index_data, 2628000);

		// Map relaated subjects.
		$subject_relations = array();
		// For each programme in output
		foreach($programmes as $programme){

			// Create arrays as needed.
			if(!isset($subject_relations[$programme->subject_area_1_8])) $subject_relations[$programme->subject_area_1_8] = array();
			if(!isset($subject_relations[$programme->subject_area_2_9])) $subject_relations[$programme->subject_area_2_9] = array();

			// Add this programme to subject
			$subject_relations[$programme->subject_area_1_8][$programme->instance_id] = $index_data[$programme->instance_id];
			// If second subject isn't the same, add it to that also
			if($programme->subject_area_1_8 != $programme->subject_area_2_9){
				$subject_relations[$programme->subject_area_2_9][$programme->instance_id] = $index_data[$programme->instance_id];
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
		parent::save();
		
		// Update feed file & kill output caches
		static::generate_api_index($revision->year);
		API::purge_output_cache();
		Cache::forget('api-programme-ug'. '-'. $revision->year . '-'. $revision->instance_id);
		
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
		$mapping = Programme::get_api_related_programmes_map($year);

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



}

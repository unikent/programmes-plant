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
		$cache_key = "api-{$tbl}.{$year}.{$iid}";
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
		$cache_key = "api-{$tbl}.{$year}.{$iid}";

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
	 * purge internal cache for Programme type in a given year (called as UG_Programe::purge_internal_cache(2015); )
	 *
	 * @param year - year to purge
	 * @return true/false
	 */
	public static function purge_internal_cache($year){
		try {
			return Cache::purge("api-programmes_ug.{$year}");
		} catch( Exception $e ) {
			return false;
		}
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
	 * get a copy of the programme fees listing (from cache if possible)
	 *
	 * @param $year year to get fees for
	 * @return programmes fees
	 */
	public static function get_api_fees($year)
	{	
		$type = static::$type;
		$cache_key = "api-index-{$type}.fees-$year";
		return (Cache::has($cache_key)) ? Cache::get($cache_key) : static::generate_api_fees($year);
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
		$study_abroad_option_field = static::get_study_abroad_option_field();

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
					 $programme_type_field,
					 $withdrawn_field,
					 $suspended_field

		);
		// If UG, add ucas field
		if ($type == 'ug') {
			$fields[] = $ucas_code_field;
		}
		// if pg add additional locations field and study abroad
		if ($type == 'pg') {
			$fields[] = $additional_locations_field;
			$fields[] = $study_abroad_option_field;
		}

		// Find all programmes that have a live revision set (live_revision != 0)
		$programmes_with_live_revisions = static::where('year','=', $year)->where('live_revision', '!=', 0)->get('live_revision');
		// pull out id's of all live revisions
		$live_revisions_ids = array();
		foreach($programmes_with_live_revisions as $programme_with_live_revisions){
			$live_revisions_ids[] = $programme_with_live_revisions->attributes['live_revision'];
		} 

		// if nothing is live in $year, don't continue
		if(empty($live_revisions_ids)){
			return array();
		}
	
		// Pull out all revisions that have there id within the above array (as these are what need to be published)
		$programmes = $revision_model::with(array('award', 'subject_area_1', 'administrative_school', 'additional_school', 'location'))
						->where_in('id', $live_revisions_ids)
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
				'new_programme' => 	isset($attributes[$new_programme_field]) ? $attributes[$new_programme_field] : '',
				'subject_to_approval' => isset($attributes[$subject_to_approval_field]) ? $attributes[$subject_to_approval_field] : '',
				'withdrawn' => isset($attributes[$withdrawn_field]) ? $attributes[$withdrawn_field] : '',
				'suspended' => isset($attributes[$suspended_field]) ? $attributes[$suspended_field] : '',
				'mode_of_study' => 	isset($attributes[$mode_of_study_field]) ? $attributes[$mode_of_study_field] : '',
				'ucas_code' 	=> 	isset($attributes[$ucas_code_field]) ? $attributes[$ucas_code_field] : '',
				'search_keywords' => isset($attributes[$search_keywords_field]) ? $attributes[$search_keywords_field] : '',
				'campus_id' => isset($relationships["location"]) ? $relationships["location"]->attributes["identifier"] : '',
				'pos_code' => isset($attributes[$pos_code_field]) ? $attributes[$pos_code_field] : '',
				'awarding_institute_or_body' => isset($attributes[$awarding_institute_or_body_field]) ? $attributes[$awarding_institute_or_body_field] : '',
				'module_session' => isset($attributes[$module_session_field]) ? $attributes[$module_session_field] : '',
				'subject2'	 => 	isset($relationships["subject_area_2"]) ? $relationships["subject_area_2"]->attributes["name"] : '',
				'programme_type' => isset($attributes[$programme_type_field]) ? $attributes[$programme_type_field] : '',
				'study_abroad_option' => isset($attributes[$study_abroad_option_field]) ? $attributes[$study_abroad_option_field] : ''
			);
			
			$statuses = '(';
			if($index_data[$attributes['instance_id']]['subject_to_approval'] == 'true'){
				$statuses .= "subject to approval";
			}
			if($index_data[$attributes['instance_id']]['withdrawn'] == 'true'){
				$statuses .= $statuses == '(' ? "withdrawn" : ", withdrawn";
			}
			if ($index_data[$attributes['instance_id']]['suspended'] == 'true') {
				$statuses .= $statuses == '(' ? "suspended" : ", suspended";
			}
			$statuses = $statuses == '(' ? '' : $statuses . ')';

			$index_data[$attributes['instance_id']]['programmme_status_text'] = $statuses;

		}


		// Store index data in to cache
		Cache::put($cache_key_index, $index_data, 2628000);

		// Map relaated subjects.
		$subject_relations = array();

		// For each programme in output
		foreach($programmes as $programme){

			$subject_area_1 = isset($programme->attributes[$subject_area_1_field]) ? $programme->attributes[$subject_area_1_field] : '';
			$subject_area_2 = isset($programme->attributes[$subject_area_2_field]) ? $programme->attributes[$subject_area_2_field] : '';
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
	 * generate new copy of programme fees data from live DB
	 *
	 * @param $year year to get fees index for
	 * @return programmes fees index
	 */
	public static function generate_api_fees($year)
	{
		$type = static::$type;
		$cache_key_index = "api-index-{$type}.fees-$year";

		// use the api index as a starting point
		$index_data = static::get_api_index($year);
		$fees_data = array();

		// Generate mapping for "taught/research" type field
		if($type == 'pg'){
			$extra_fields = array();
			$model = API::get_programme_model();
			$programme_type_field = static::get_programme_type_field();
			$extra = $model::where('year','=',$year)->get(array('instance_id', $programme_type_field));
			foreach($extra as $fields){
				$extra_fields[$fields->attributes["instance_id"]] = $fields->attributes[$programme_type_field];
			}
		}

		// create fees data for each programme
		foreach ($index_data as $programme) {


			if($type == 'ug'){
				$fee = Fees::getFeeInfoForPos($programme['pos_code'], $year);
				$currency = (!empty($fee['home']['euro-full-time']) || !empty($fee['home']['euro-part-time'])) ? 'euro' : 'pound';
				$programme_data = array(
					'id' 				=> 		$programme['id'],
					'name' 				=> 		$programme['name'],
					'slug' 				=> 		$programme['slug'],
					'award' 			=> 		$programme['award'],
					'mode_of_study'		=>		$programme['mode_of_study'],
					'search_keywords' 	=> 		$programme['search_keywords'],
					'pos_code'			=>		$programme['pos_code'],
					'currency'			=>		$currency,
					'home_full_time'	=>		$currency == 'pound' ? $fee['home']['full-time'] : $fee['home']['euro-full-time'],
					'home_part_time'	=>		$currency == 'pound' ? $fee['home']['part-time'] : $fee['home']['euro-part-time'],
					'int_full_time'		=>		$currency == 'pound' ? $fee['int']['full-time'] : $fee['int']['euro-full-time'],
					'int_part_time'		=>		$currency == 'pound' ? $fee['int']['part-time'] : $fee['int']['euro-part-time']
				);

				$fees_data[] = $programme_data;
			}

			else{

				$deliveries = PG_Delivery::get_programme_deliveries($programme['id'], $year);
				foreach ($deliveries as $delivery) {
					if(empty($delivery['description'])){
						continue;
					}
					$fee = Fees::getFeeInfoForPos($delivery['pos_code'], $year);
					$currency = (!empty($fee['home']['euro-full-time']) || !empty($fee['home']['euro-part-time'])) ? 'euro' : 'pound';
					$delivery_awards = PG_Award::replace_ids_with_values($delivery['award'],false,true);
					$delivery['award_name'] = isset($delivery_awards[0]) ? $delivery_awards[0] : '';

					// We get a description:
					// Drama & Theatre - Physical Actor Training & Performance with a Term in Moscow - MA - Full-time at Canterbury 
					//
					// But need only the name (which can have an arbitary number of -'s in it) only the hope it wont change much, just explode
					// -'s and cut off the last 2 elements to give us:

					// Drama & Theatre - Physical Actor Training & Performance with a Term in Moscow 
					$description  = trim(implode(' - ',array_slice(explode(' - ', $delivery['description']), 0, -2)));

					$programme_data = array(
						'id' 				=> 		$programme['id'],
						'name' 				=>		$description,
						'slug' 				=>		$programme['slug'],
						'award' 			=>		$delivery['award_name'],
						'mode_of_study'		=>		$programme['mode_of_study'],
						'search_keywords' 	=>		$programme['search_keywords'],
						'pos_code'			=>		$delivery['pos_code'],
						'type'				=>		$extra_fields[$programme['id']], // get course type
						'currency'			=>		$currency,
						'home_full_time'	=>		$currency == 'pound' ? $fee['home']['full-time'] : $fee['home']['euro-full-time'],
						'home_part_time'	=>		$currency == 'pound' ? $fee['home']['part-time'] : $fee['home']['euro-part-time'],
						'int_full_time'		=>		$currency == 'pound' ? $fee['int']['full-time'] : $fee['int']['euro-full-time'],
						'int_part_time'		=>		$currency == 'pound' ? $fee['int']['part-time'] : $fee['int']['euro-part-time']
					);

					$key = trim(substr($delivery['mcr'], 0, strpos($delivery['mcr'], "-")));

					$fees_data[$key] = $programme_data;
				}
			}
		}

		$fees_data = array_values($fees_data);
		
		// Store index data in to cache
		Cache::put($cache_key_index, $fees_data, 2628000);

		
		// return
		return $fees_data;
	}

	// Get deliveries for this programme
	public function get_deliveries()
	{
		$delivery_class = static::$type . "_Delivery";
		return $delivery_class::where('programme_id','=',$this->id)->get();
	}

	public function deliveries()
	{
	  	return $this->has_many(static::$type . '_delivery', 'programme_id');
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
		
		$this->live_revision = 0;
		$this->raw_save();
		
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
	 * Find related programmes using API. Returns array containing any course
	 * in the given year that is in either subject_1 or subject_2.
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

		if ($subject_1 != $subject_2)
		{
			foreach ($mapping[$subject_2] as $programme)
			{
				$mapping[$subject_1][$programme['id']] = $programme;
			}
		}

		$related_courses_array = isset($mapping[$subject_1]) ? $mapping[$subject_1] : array();

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

	public function use_revision($revision){
		$revision = parent::use_revision($revision);
		
		// if the revision being used was last edited by a user 
		// who does not have the permission to approve revisions,
		// lock this programme to that user, else no need to lock the programme
		$user = User::where('username', '=', $revision->edits_by)->first();
		if($user->can('approve_revisions')){
			$this->locked_to = '';
			parent::save();
		}
		else{
			$this->locked_to = $user->username;
			parent::save();
		}

		return $revision;
	}


}

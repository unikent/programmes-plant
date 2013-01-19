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
	 * look through the passed in record and substitute any ids with data from their correct table
	 * primarily for our json api
	 * 
	 * @param $record The record
	 * @return $new_record A new record with ids substituted
	 */
	public static function pull_external_data($record)
	{
		$path = path('storage') . 'api/';
		$programme_fields_path = $path . 'programmefield.json';

		//if we dont have a json file, return the $record as it was
		if (!file_exists($programme_fields_path))
		{
			return $record;
		}

		//get programme fields
		$programme_fields = json_decode(file_get_contents($programme_fields_path));
		
		//make neater programme fields array
		$fields_array = array();
		foreach ($programme_fields as $field) {
			$fields_array[$field->colname] = $field->field_meta;
		}
		
		//substitute the concerned ids with actual data
		$new_record = array();
		foreach ($record as $field_name => $field_value) {
			if(isset($fields_array[$field_name])){
				$model = $fields_array[$field_name];
				$field_value = $model::replace_ids_with_values($field_value);
			}
			$new_record[$field_name] = $field_value;
		}

		return $new_record;
	}

	/**
	 * This function replaces the passed-in ids with their actual record
	 * Limiting the record to its name and id
	 */
	public static function replace_ids_with_values($ids){

		$ds_fields = static::where_in('id', explode(',', $ids))->get();
		$values = array();
		foreach ($ds_fields as $ds_field) {
			$title_field = static::get_title_field();
			$slug_field = static::get_slug_field();
			$values[$ds_field->id] = static::remove_ids_from_field_names(array(
					'id' => $ds_field->id,
					$title_field => $ds_field->$title_field,
					$slug_field => $ds_field->$slug_field
				));
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
	 * Returns the JSONified index of programmes for a given year.
	 * 
	 * @param string $year The year of the programmes.
	 * @param string $level The level of the programmes, ug (undergraduate) or pg (postgraduate)
	 * @return string The JSON index directly from disc cache.
	 */
	public static function json_index($year, $level)
	{
		$path = path('storage') . 'api/' . $level . '/' . $year . '/';

		if (! file_exists($path . 'index.json'))
		{
			return false;
		}

		return file_get_contents($path . 'index.json');
	}

	/**
	 * Get flattened data for a programme from the JSON disc cache.
	 * 
	 * @param string $year The year of the programme.
	 * @param string $level The level of the programme, ug (undergraduate) or pg (postgraduate).
	 * @param int $programme_id The ID of the programme.
	 * @return Object $final | false The object as an array with relevant data attached. or false if we had problems.
	 */
	public static function get_as_flattened($year, $level, $programme_id)
	{
		// Set up the path to the output/cache file.
		$path = path('storage') . 'api/' . $level . '/' . $year . '/';
		
		// Try to get JSON files for global and programme settings, as well as the programme data itself.
		// If 
		if (! file_exists($path . 'globalsetting.json') or ! file_exists($path . 'programmesetting.json') or ! file_exists($path . $programme_id . '.json') )
		{
			return false;
		}

		// If the cache files do exist for global/programme settings and the programme data, put them into objects.
		$global_settings = json_decode(file_get_contents($path . 'globalsetting.json'));
		$programme_settings = json_decode(file_get_contents($path . 'programmesetting.json'));
		$programme = json_decode(file_get_contents($path . $programme_id . '.json'));
		
		// In local and test environments we're using a faked JSON file rather than one generated from the sds web service.
		$modules = '';
		if (Request::env() == 'test' or Request::env() == 'local')
		{
			if(file_exists($path . $programme_id . '_modules_test.json'))
			{
				$modules = json_decode(file_get_contents($path . $programme_id . '_modules_test.json'));
			}
		}
		else
		{
			if(file_exists($path . $programme_id . '_modules.json'))
			{
				$modules = json_decode(file_get_contents($path . $programme_id . '_modules.json'));
			}
		}
		
		// Build up $final which will be an object with all the data in we need.
		// Start with the global settings.
		$final = $global_settings;
		
		// Modules
		// Note that the web service contains two levels we won't need: response and rubric. This may need fixing once we get the finished web service.
		if(!empty($modules))
		{
			$final->modules = $modules->response->rubric;
		}
			
		// Add programme settings to the $final object
		// No inheritance needed so just loop through the settings, adding them to the object.
		foreach($programme_settings as $key => $value)
		{
			$final->{$key} = $value;
		}

		// Pull in all programme dependencies eg an award id 1 will pull in all that award's data.
		// Loop through them, adding them to the $final object.
		$programme = Programme::pull_external_data($programme);

		foreach($programme as $key => $value)
		{
			// Make sure any existing key in the $final object gets updated with the new $value.
			if(!empty($value) ){
				$final->{$key} = $value;
			}
		}
		
		// Tidy up.
		foreach(array('id','global_setting_id') as $key)
		{
			unset($final->{$key});
		}
		
		// Now remove IDs from our field names, they're not necessary and return.
		// e.g. 'programme_title_1' simply becomes 'programme_title'.
		return Programme::remove_ids_from_field_names($final);
	}

	//Override generate api function to call local methods
	public static function generate_api_data($year = false, $revision = false){

		print_r($revision);
		die();
		static::generate_api_programme($revision->programme_id, $year, $revision);
		static::generate_api_index($year);
	}

	public static function get_api_programme($id, $year){
		$cache_key = "api-programme-ug-$year-$id";
		return (Cache::has($cache_key)) ? Cache::get($cache_key) : static::generate_api_programme($id, $year);
	}

	public static function generate_api_programme($id, $year, $revision = false){
		$cache_key = "api-programme-ug-$year-$id";

		$model = get_called_class();
		$cache_key = 'api-'.$model.'-'.$year;

		// If revision not passed, get data
		if(!$revision){
			$revision = ProgrammeRevision::where('programme_id', '=', $id)->where('status', '=', 'live')->where('year', '=', $year)->first();
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

	// Get api index (attempt to use cache)
	public static function get_api_index($year){
		$cache_key = 'api-index';
		return (Cache::has($cache_key)) ? Cache::get($cache_key) : static::generate_api_index($year);
	}
	// Generate API index from live database
	public static function generate_api_index($year){
		// Set cache key
		$cache_key = "api-index";

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
		$programmes = ProgrammeRevision::where('year','=', $year)
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

		// Store data in to cache
		Cache::put($cache_key, $index_data, 2628000);

		// return
		return $index_data;
	}

}

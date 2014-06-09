<?php

class API_Controller extends Base_Controller {

	public $restful = true;

	/**
	 * Array to store headers as header => value.
	 * 
	 * Static so that potentially other classes could arbitarily add or modify headers here.
	 */
	public static $headers = array();
	
	public function __construct()
	{
		// turn off the profiler because this interferes with the web service
		Config::set('application.profiler', false);

		static::$headers['Cache-Control'] = 'public, max-age=600'; 
	}
	
	/**
	* Get the index data
	*
	* @param  int     $year     Year of index to get.
	* @param  string  $format   Format, either XML or JSON.
	* @return string  json|xml  Data as a string or HTTP response.
	*/
	public function get_index($year, $level, $format = 'json')
	{
		// get last generated date
		$last_generated = API::get_last_change_time();
		
		// If cache is valid, send 304
		if($this->cache_still_valid($last_generated)){
			return Response::make('', '304');
		}

		$index_data = API::get_index($year);

		// 204 is the HTTP code for No Content - the result processed fine, but there was nothing to return.
		if (! $index_data)
		{
			// Considering 501 (server error) as more desciptive
			// The server either does not recognize the request method, or it lacks the ability to fulfill the request
			return Response::make('', 501);
		}

		static::$headers['Last-Modified'] = API::get_last_change_date_for_headers($last_generated);

		// Return the cached index file with the correct headers.
		return ($format=='xml') ? static::xml($index_data) : static::json($index_data, 200);
	}


	/**
	 * Routing for /$year/$type/courses
	 *
	 * eg http://webtools.kent.ac.uk/api/2014/postgraduate/courses
	 * Provides a list of courses in a simple csv format
	 *
	 * @param int    $year The year.
	 * @param string $type Undergraduate or postgraduate.
	 */
	public function get_simplelist($year, $type)
	{
		// get last generated date
		$last_generated = API::get_last_change_time();
		// If cache is valid, send 304
		if($this->cache_still_valid($last_generated)) return Response::make('', '304');

		// get the list of courses
		$programmes = API::get_index($year); $listing = array();

		// Generate data format
		foreach($programmes as $programme) {
			$output = array();
			$output['id'] = $programme['id'];
			$output['title'] = $programme['name'];
			$output['awards'] = $programme['award'];
			$output['subject to approval'] = $programme['subject_to_approval'];

			if($type == 'undergraduate') $output['ucas code'] = $programme['ucas_code'];
			if($type == 'postgraduate') $output['taught/research'] = $programme['programme_type'];

			$output['location'] = $programme['campus'];
			if($type == 'postgraduate') $output['additional locations'] = $programme['additional_locations'];
			
			$lising[] = $output;
		}

		// output the data
		return static::csv_download($lising, "courses", $last_generated);
	}

	/**
	 * Routing for /$year/$type/course-ids
	 *
	 * eg http://webtools.kent.ac.uk/api/2014/postgraduate/course-ids
	 * Provides a list of courses and ids in a simple csv format
	 *
	 * @param int    $year The year.
	 * @param string $type Undergraduate or postgraduate.
	 */
	public function get_verysimplelist($year, $type)
	{
		// get last generated date
		$last_generated = API::get_last_change_time();
		// If cache is valid, send 304
		if($this->cache_still_valid($last_generated)) return Response::make('', '304');

		// get the list of courses
		$programmes = API::get_index($year); $listing = array();

		// Generate data format
		foreach($programmes as $programme) {
			$output = array();
			$output['id'] = $programme['id'];
			$output['title'] = $programme['name'] . ' ' . $programme['award'];
			$output['title'] .= $programme['subject_to_approval'] != '' ? ' (subject to approval)' : '';
			
			$lising[] = $output;
		}

		// output the data
		return static::csv_download($lising, "course-ids", $last_generated);
	}

	/**
	 * Routing for /$year/$type/print-courses
	 *
	 * eg http://webtools.kent.ac.uk/api/2014/postgraduate/print-courses
	 * Provides a list of courses in a simple csv format customised for print output purposes
	 *
	 * @param int    $year The year.
	 * @param string $type Undergraduate or postgraduate.
	 */
	public function get_printlist($year, $type, $start)
	{

		// get last generated date
		$last_generated = API::get_last_change_time();
		// If cache is valid, send 304
		if($this->cache_still_valid($last_generated)) return Response::make('', '304');

		// get the list of courses
		$api_index = API::get_index($year, 'pg');
		$listing = array();
		$data = array();
        // fetch each programme individually for our xcri feed
        $index_programmes = array_keys($api_index);

        $start--;
    	$slice = array_slice($index_programmes, (int)$start, 50);

        foreach ($slice as $programme_id) {
	        try {
	            $data['programmes'][] = API::get_programme('pg', $year, $programme_id);
	        }
	        catch (Exception $e) {
	        }
    	}

		foreach($data['programmes'] as $programme) {

			$output = array();

			$id = $programme['instance_id'];

			// pull out awards and combine into a comma separated list
			$programme['award_list'] = '';
			foreach ($programme['award'] as $award) if (!empty($award['name'])) $programme['award_list'] .= $award['name'] . ', ';
			$programme['award_list'] = substr($programme['award_list'], 0, -2); // cuts off the final comma+space

			// now set the programme title which is title + awards
			$output['title'] = $programme['programme_title'];
			$output['title'] .= ' (' . $programme['award_list'] . ')';
			$output['title'] .=  $programme['subject_to_approval'] == 'true' ? " (subject to approval)" : '';

			if ($programme['programme_suspended'] == 'true' || $programme['programme_withdrawn'] == 'true') {
				$output['overview'] = $programme['holding_message'];
			}
			else {
				$output['overview'] = $programme['schoolsubject_overview'];

				// course structure data
				$output['course structure']  = 'Course structure' . "\r\n\r\n";
 				if (!empty($programme['programme_overview'])) {
 					$output['course structure'] .= $programme['programme_overview']; 
 				}

 				$output['course structure'] .= 'Modules' . "\r\n";

 				$emptystages = true;
 				if ( isset ($programme['modules']) ) {
					foreach($programme['modules'] as $module){
						if ( ! empty($module->stages) ) {
							$emptystages = false;
						}
					}
				}

				if ((empty($programme['modules'][0])) || $emptystages) {
					if ( !empty($programme['modules_intro_no_pos']) ) {
						$output['course structure'] .= $programme['modules_intro_no_pos'];
					}
				}
				else {
					$output['course structure'] .= $programme['modules_intro'];
				}

				// get modules from all deliveries as unique lists
				$module_list = array();
				if (!empty($programme['modules'])) {
					foreach($programme['modules'] as $delivery_modules) {
						if (!empty($delivery_modules->stages)) {
							foreach($delivery_modules->stages as $stage){
								foreach($stage->clusters as $clusters){
									foreach($clusters as $cluster){
										foreach($cluster->modules as $modules){
											foreach($modules as $module){
												//skip blanks
												if ($module->module_code=='') continue;
												// index on module code, so duplicates will just overwrite each other
												$module_list[$module->module_code] = $module;
											}
										}
									}
								}
							}
						}
					}
				}

				foreach($module_list as $module) {
					$output['course structure'] .= "$module->module_code  -  $module->module_title" . "\r\n" . "Credits: $module->credit_amount credits ( $module->ects_credit ECTS credits)." . "\r\n";
	            }
	            $output['course structure'] .= 'Assessment' . "\r\n" . $programme['assessment'];

	            // study support
	            $output['study support'] = 'Study support' . "\r\n";
				$output['study support'] = !empty($programme['key_information_miscellaneous']) ? $programme['key_information_miscellaneous'] . "\r\n" : "\r\n";

				if ( !empty($programme['careers_and_employability']) || !empty($programme['globals']['careersemployability_text']) ) {
					$output['study support'] .= "Careers and employability" . "\r\n" . $programme['careers_and_employability'] . "\r\n";
					$output['study support'] .= $programme['globals']['careersemployability_text'] . "\r\n";
				}

				if(!empty($programme['professional_recognition'])) {
					$output['study support'] .= "Professional recognition" . "\r\n";
					$output['study support'] .= $programme['professional_recognition'] . "\r\n";
				}

				if( ! empty($programme['did_you_know_fact_box']) ) {
					$output['study support'] .= "National ratings" . "\r\n";
					$output['study support'] .= $programme['did_you_know_fact_box'] . "\r\n";
				}

				// entry requirements
				$output['entry requirements'] = 'Entry requirements' . "\r\n";
				$output['entry requirements'] .= $programme['entry_requirements'] . "\r\n";
				$output['entry requirements'] .= "General entry requirements" . "\r\n";
				$output['entry requirements'] .= $programme['globals']['pg_general_entry_requirements'] . "\r\n";
				$output['entry requirements'] .= (!empty($programme['english_language_requirements_intro_text'])) ? "English language entry requirements" . "\r\n" . $programme['english_language_requirements_intro_text'] . "\r\n" : "\r\n";


				// Research areas
				$output['research areas'] = 'Research areas' . "\r\n";
				$output['research areas'] .= $programme['research_groups'];

				// Staff research interests
				// $output['staff research interests'] = "Staff research interests" . "\r\n";

				// if ( strstr($programme['programme_type'], 'research') ) {
				// 	$output['staff research interests'] .= $programme['staff_research_interests_intro']. "\r\n";
				// }

				// if (!empty($programme['staff_profile_links'])) {
				// 	$output['staff research interests'] .= $programme['staff_profile_links']. "\r\n";
				// }
				// elseif (!empty($programme['staff_profiles'])) {
				// 	$output['staff research interests'] .= "Full details of staff research interests can be found at {$programme['staff_profiles']}" . "\r\n";
				// }

				// if (!empty($programme['staff_research_interests'])) {
				// 	foreach ( $programme['staff_research_interests'] as $staff ) {
				// 	 	if ( $staff['hidden'] == 0 ) {
				// 			$output['staff research interests'] .= $staff['title'] != '' ? $staff['title'] . ' '  : '';
				// 			$output['staff research interests'] .= $staff['forename'] . " " . $staff['surname'];
				// 			$output['staff research interests'] .= $staff['role'] != '' ? ' : ' . $staff['role'] : '';
				// 			$output['staff research interests'] .= "\r\n";
							
				// 			if ( ! empty ($staff['blurb']) ) {
				// 				$output['staff research interests'] .= $staff['blurb']. "\r\n";
				// 				if ($staff['profile_url'] != '') { 
				// 					$output['staff research interests'] .= $staff['profile_url']. "\r\n";
				// 				}
				// 			}
				// 		}
				// 	}
				// }

				// key facts
				$output['key facts'] = 'Key facts' . "\r\n";
				if (!empty($programme['additional_school'][0]) && !empty($programme['administrative_school'][0])) { 
					$output['key facts'] .= "Schools: " . $programme['school_website'] . " " . $programme['administrative_school'][0]['name'] . ", " . $programme['url_for_additional_school'] . " " . $programme['additional_school'][0]['name'] . "\r\n";
				}
				else if (!empty($programme['administrative_school'][0])) {
					$output['key facts'] .= $programme['school_website'] . " " . $programme['administrative_school'][0]['name'] . "\r\n";
				}

				// is there a second subject area?
				$second_subject = (isset($programme['subject_area_2'][0]) && $programme['subject_area_2'][0] != null);
				$output['key facts'] .= "Subject area: ";
				$output['key facts'] .= $second_subject ? 's' : '';
				$output['key facts'] .= $programme['subject_area_1'][0]['name'];
				$output['key facts'] .= $second_subject ? ', ' . $programme['subject_area_2'][0]['name'] : '';
				$output['key facts'] .= "\n\n";
				$output['key facts'] .= "Award: " . $programme['award_list'] . "\r\n";
				
				$output['key facts'] .= "Course type: ";
				if(strpos($programme['programme_type'], 'research') === false ) $output['key facts'] .= "Taught";
				elseif(strpos($programme['programme_type'], 'taught') === false) $output['key facts'] .= "Research";
				else $output['key facts'] .= "Taught-research";
				$output['key facts'] .=  "\r\n";

				$output['key facts'] .= "Location: ";
				$locations = "{$programme['location'][0]['url']}" . " " . $programme['location'][0]['name'];
				$additional_locations = '';
				if ($programme['additional_locations'] != "") {
					foreach ($programme['additional_locations'] as $key=>$additional_location) {
						if ($additional_location != '') {
							if ( $key == (sizeof($programme['additional_locations'])-1) ) {
								$additional_locations .= " and " . $additional_location['url'] . " " . $additional_location['name'];
							}
							else {
								$additional_locations .= ", " . $additional_location['url'] . " " . $additional_location['name'];
							}
						}
					}
				}
				$output['key facts'] .= $locations.$additional_locations . "\r\n";
				

				$output['key facts'] .= "Mode of study: {$programme['mode_of_study']}" . "\r\n";

				if (!empty($programme['attendance_mode'])) {
					$output['key facts'] .= "Attendance mode: {$programme['attendance_mode']}" . "\r\n";
				}

				if (!empty($programme['attendance_text'])) {
					$output['key facts'] .= "Duration: {$programme['attendance_text']}" . "\r\n";
				} 
				
				if (!empty($programme['start'])) {
					$output['key facts'] .= "Start: {$programme['start']}" . "\r\n";
				}
				
				if (!empty($programme['accredited_by'])) {
					$output['key facts'] .= "Accredited by: {$programme['accredited_by']}" . "\r\n";
				}
				
				if (!empty($programme['total_kent_credits_awarded_on_completion'])) { 
					$output['key facts'] .= "Total Kent credits: {$programme['total_kent_credits_awarded_on_completion']}" . "\r\n";
				}
			
				if (!empty($programme['total_ects_credits_awarded_on_completion'])) {
					$output['key facts'] .= "Total ECTS credits: {$programme['total_ects_credits_awarded_on_completion']}" . "\r\n";
				}

				$output['key facts'] .= "Postgraduate fees and funding information: http://www.kent.ac.uk/courses/funding/postgraduate/index.html";

			}

			$listing[] = $output;
		}

		// output the data
		return static::csv_download($listing, "courses", $last_generated);
		
		
	}

	/**
	 * Routing for /export/$year/$type/kis
	 *
	 * Export CSV of data for KIS
	 *
	 * @param int    $year The year.
	 * @param string $type Undergraduate or postgraduate.
	 */
	public function get_export_kisdata($year, $type)
	{
		// get last generated date 
		$last_generated = API::get_last_change_time();
		// If cache is valid, send 304
		if($this->cache_still_valid($last_generated)) return Response::make('', '304');

		// Get real year
		if($year == 'current') $year = Setting::get_setting(URLParams::$type."_current_year");

		// get the list of courses
		$programmes = API::get_index($year); 
		$model = API::get_programme_model();
		$listing = array();

		// Additional Fields
		$kiscourseid_field = $model::get_kiscourseid_field();
		$total_credit_field = $model::get_total_kent_credits_awarded_on_completion_field();
		
		// Generate data format
		foreach($programmes as $programme) {
			$output = array();
			$output['POS'] = $programme['pos_code'];
			$output['Title'] = $programme['name'];

			if($type == 'undergraduate') $output['UCAS code'] = $programme['ucas_code'];

			$output['Honours type'] = $programme['award'];
			$output['Location'] = $programme['campus'];
			$output['Mode of study'] = $programme['mode_of_study'];

			// pulled from "current" for speed, may have to be adjusted to use revisions if live & current are too out of sync.
			$extra = $model::where('instance_id','=',$programme['id'])->where('year','=',$year)->first(array($kiscourseid_field, $total_credit_field));
			$output['KIS Course ID'] = $extra->attributes[$kiscourseid_field];
			$output['Total Kent credits'] = $extra->attributes[$total_credit_field];

			$output['URL'] = "http://kent.ac.uk/courses/{$type}/{$year}/{$programme['id']}/{$programme['slug']}";
		
			$lising[] = $output;
		}

		// output the data
		return static::csv_download($lising, "{$year} KIS Export", $last_generated);
	}


	/**
	 * Routing for /export/$year/$type/entry
	 *
	 * Export CSV of Entry Data
	 *
	 * @param int    $year The year.
	 * @param string $type Undergraduate or postgraduate.
	 */
	public function get_export_entrydata($year, $type)
	{
		// UG only currently
		if($type !== 'undergraduate') return 0;

		// get last generated date 
		$last_generated = API::get_last_change_time();
		// If cache is valid, send 304
		if($this->cache_still_valid($last_generated)) return Response::make('', '304');

		// Get real year
		if($year == 'current') $year = Setting::get_setting(URLParams::$type."_current_year");

		// get the list of courses
		$programmes = API::get_index($year); 
		$model = API::get_programme_model();
		$listing = array();
	
		// Additional Fields
		$extra_cols = array(
			"subject_to_approval", 
			"homeeu_students_intro_text", 
			"a_level", 
			"cgse",
			"access_to_he_diploma",
			"btec_level_3_extended_diploma_formerly_btec_national_diploma",
			"btec_level_5_hnd",
			"international_baccalaureate"
		);
		$fields = array();
		foreach($extra_cols as $col){
			$tmp_f = "get_{$col}_field";
			$fields[$col] = $model::$tmp_f();
		}

		// Generate data format
		foreach($programmes as $programme) {
			$output = array();

			// pulled from "current" for speed, may have to be adjusted to use revisions if live & current are too out of sync.
			$extra = $model::where('instance_id','=',$programme['id'])->where('year','=',$year)->first($fields);

			$output['URL ID'] = $programme['id'];
			$output['Title'] = $programme['name'];
			$output['STA'] = $extra->attributes[$fields["subject_to_approval"]];
			if($type == 'undergraduate') $output['UCAS code'] = $programme['ucas_code'];
			$output['POS Code'] = $programme['pos_code'];

			$output['Home/EU students intro text'] = strip_tags($extra->attributes[$fields["homeeu_students_intro_text"]]);
			$output['A level'] = strip_tags($extra->attributes[$fields["a_level"]]);
			$output['GCSE'] = strip_tags($extra->attributes[$fields["cgse"]]);
			$output['Access to HE'] = strip_tags($extra->attributes[$fields["access_to_he_diploma"]]);
			$output['BTEC3'] = strip_tags($extra->attributes[$fields["btec_level_3_extended_diploma_formerly_btec_national_diploma"]]);
			$output['BETC 5'] = strip_tags($extra->attributes[$fields["btec_level_5_hnd"]]);
			$output['IB'] = strip_tags($extra->attributes[$fields["international_baccalaureate"]]);

			$lising[] = $output;
		}

		// output the data
		return static::csv_download($lising, "{$year} entry data export", $last_generated);
	}

	/**
	 * Routing for /export/$year/$type/courses-without-fees
	 *
	 * Export courses without fees
	 *
	 * @param int    $year The year.
	 * @param string $type Undergraduate or postgraduate.
	 */
	public function get_courses_without_fees($year, $type)
	{
		// get last generated date 
		$last_generated = API::get_last_change_time();
		// If cache is valid, send 304
		if($this->cache_still_valid($last_generated)) return Response::make('', '304');

		// Get real year
		if($year == 'current') $year = Setting::get_setting(URLParams::$type."_current_year");

		// get the list of courses
		$programmes = API::get_index($year); 
		$model = API::get_programme_model();
		$listing = array();
		$level = $type == 'undergraduate' ? 'ug' : 'pg';
		
		// // Generate data format
		foreach($programmes as $programme) {
			$output = array();

			$output['ID'] = $programme['id'];
			$output['Title'] = $programme['name'];

			$programme_api = array();
			try{
				$programme_api = API::get_programme($level, $year, $programme['id']);
			} catch(Exception $e) {
				continue;
			}
			
			// PG
			if ($level == 'pg') {
				if (isset($programme_api['deliveries'])) {

					$pos_without_fees = "";

					foreach ($programme_api['deliveries'] as $delivery) {
						if (!isset($delivery['fees']) || empty($delivery['fees'])) {
							$pos_without_fees .= empty($pos_without_fees) ?  $delivery['pos_code'] : ', ' . $delivery['pos_code'];
							$pos_without_fees = trim($pos_without_fees);
						}
					}

					if (empty($pos_without_fees)) {
						continue;
					}

					$output['POS WITHOUT FEES'] = $pos_without_fees;
				}
			}

			// UG
			else {
				if (!empty($programme_api['fees'])) {
					continue; // this ug programme has fees so skip it
				}
				$output['POS'] = $programme['pos_code'];
			}
		

			if($type == 'undergraduate') $output['UCAS code'] = $programme['ucas_code'];

			$output['URL'] = "http://kent.ac.uk/courses/{$type}/{$year}/{$programme['id']}/{$programme['slug']}";
			$listing[] = $output;
		}
			
		// output the data
		return static::csv_download($listing, "{$year} {$type} courses without fees", $last_generated);
	}



	/**
	* Get subjects index
	*
	* @param  int     $year     Year of index to get.
	* @param  string  $format   Format, either XML or JSON.
	* @return string  json|xml  Data as a string or HTTP response.
	*/
	public function get_subject_index($year, $format = 'json')
	{
		// Get last updated date from cache
		$last_generated = API::get_last_change_time();
		// If cache is valid, send 304
		if($this->cache_still_valid($last_generated)){
			return Response::make('', '304');
		}

		//Get subjects
		$subjects = API::get_subjects_index($year, 'ug');

		if (! $subjects) return Response::make('', 501);

		static::$headers['Last-Modified'] = API::get_last_change_date_for_headers($last_generated);

		// output
		return ($format=='xml') ? static::xml($subjects) : static::json($subjects, 200);
	}

	/**
	* Get data for the programme as JSON.
	*
	* @param string $year          The Year.
	* @param string $level 		   undergraduate or postgraduate
	* @param int    $programme_id  The programme we're pulling data for.
	* @param string $format        Return in XML or JSON.      
	* @return Response             json|xml data as a string or HTTP response.    
	*/
	public function get_programme($year, $level, $programme_id, $format = 'json')
	{
		switch($level){
			case 'postgraduate':
				$level = 'pg';
				break;
			case 'undergraduate':
				$level = 'ug';
				break;
		}

		// If cache is valid, send 304
		$last_modified = API::get_last_change_time();

		if($this->cache_still_valid($last_modified))
		{
			return Response::make('', '304');
		}

		try 
		{
			$programme = API::get_programme($level, $year, $programme_id);
		}
		
		// Required data is missing?
		catch(MissingDataException $e)
		{
			return Response::make('', 501);
		}
		catch(NotFoundException $e)
		{
			return Response::make('', 404);
		}
		
		// Unknown issue with data.
		if (! $programme)
		{
			return Response::make('', 501);
		}

		// Set the HTTP Last-Modified header to the last updated date.
		static::$headers['Last-Modified'] = API::get_last_change_date_for_headers($last_modified);
		
		// return a JSON version of the newly-created $final object
		return ($format=='xml') ? static::xml($programme) : static::json($programme, 200);
	}

	/**
	 * get_data Return data from simpleData cache
	 *
	 * This is a wrapper around get_data_for_level to avoid
	 * breaking changes to the public API. Some lookups require
	 * a level, others do not. As Laravel 3 does not support named routes 
	 * we can't make level an optional parameter and require different
	 * endpoints for the router.
	 *
	 * @param string $type.   Type of data to return, ie. Campuses
	 * @param string $format  Return in JSON or XML.
	 * @return Response       json|xml data as a string or HTTP response.    
	 */
	public function get_data($type, $format = 'json')
	{
		return $this->get_data_for_level(null, $type, $format);
	}

	/**
	 * get_data_for_level Return data from simpleData cache
	 *
	 * @param string $level   Undergraduate, Postgraduate 
	 * @param string $type    Type of data to return, ie. Campuses
	 * @param string $format  Return in JSON or XML.
	 * @return Response       json|xml data as a string or HTTP response.    
	 */
	public function get_data_for_level($level, $type, $format = 'json'){
		switch($level){
			case 'undergraduate':
				$level = 'ug';
				break;
			case 'postgraduate':
				$level = 'pg';
				break;
		}

		// If cache is valid, send 304
		$last_modified = API::get_last_change_time();

		if($this->cache_still_valid($last_modified)){
			return Response::make('', '304');
		}

		// Set data for cache
		static::$headers['Last-Modified'] = API::get_last_change_date_for_headers($last_modified);

		// If data exists, send it, else 404
		try
		{
			$data = API::get_data($type, $level);
			return ($format=='xml') ? static::xml($data) : static::json($data, 200);
		}
		catch(NotFoundException $e)
		{
			return Response::make('', 404);
		}
	}

	/**
	 * Is cache still valid?
	 *
	 * @param int $last_modified.  Unix timestamp of when last change to system data was made.
	 * @return bool true|false     If cached data is still valid.
	 */
	protected function cache_still_valid($last_modified)
	{
		// There is no cache (hence its invalid)
		if(!Request::header('if-modified-since')) return false;

		// Unknown data of last change, to be safe assume cache is invalid
		if($last_modified === null) return false;

		// Get "if-modified-since" header as a timestamp
		$last_retrived = Request::header('if-modified-since');
		$request_modified_since = strtotime($last_retrived[0]);

		// If time the client created its cache ($request_modified_since) is after (or equal to) 
		// the last change made to the data ($last_modified) then it is still valid.
		return ($last_modified <= $request_modified_since);
	}

	/**
	 * Get preview
	 *
	 * @param  string $hash   The hash of the preview.
	 * @return string $format The format of the response, JSON or XML.
	 */
	public function get_preview($level, $hash, $format='json')
	{
		try 
		{
			$programme = API::get_preview($hash);
		}
		catch(NotFoundException $e)
		{
			// Required data is missing?
			return Response::make('', 404);
		}

		return ($format=='xml') ? static::xml($programme) : static::json($programme);
	}


	/**
	* Output as XML
	*
	* @param mixed $data         To be shown as XML
	* @param int   $code         HTTP code to return.
	* @param array $add_headers  Additional headers to add to output.
	*/
	public static function xml($data, $code = 200, $add_headers = false)
	{
		static::$headers['Content-Type'] = 'application/xml';

		if ($add_headers)
		{
			$headers = array_merge(static::$headers, $add_headers);
		}

		return Response::make(API::array_to_xml($data), 200, static::$headers);
	}
	
	/**
	* Output as JSON
	*
	* @param mixed $data        To be shown as JSON.
	* @param int   $code        HTTP code to return.
	* @param array $add_headers Additional headers to add to output.
	*/
	public static function json($data, $code = 200, $add_headers = false)
	{
		static::$headers['Content-Type'] = 'application/json';

		if ($add_headers)
		{
			static::$headers = array_merge(static::$headers, $add_headers);
		}

		return Response::json($data, $code, static::$headers);
	}

	/**
	* Output as CSV
	*
	* @param mixed $data 	Data to be output
	* @param string   $filename 	HTTP code to return.
	* @param $last_generated 	Last generated timestamp
	*/
	public static function csv_download($output, $name, $last_generated) {
		// 204 is the HTTP code for No Content - the result processed fine, but there was nothing to return.
		if (! $output )
		{
			// Considering 501 (server error) as more desciptive
			// The server either does not recognize the request method, or it lacks the ability to fulfill the request
			return Response::make('', 501);
		}

		// set the headers for last-modified and to make sure the csv file gets downloaded
		static::$headers['Content-Type'] = 'text/csv';
		static::$headers['Last-Modified'] = API::get_last_change_date_for_headers($last_generated);
		static::$headers['Content-Disposition'] =  'attachment;filename='.$name.'.csv';

		// Header line
		$headings = array_keys( current($output) );
		$csv = API::array_to_csv($headings). "\r\r\n";
		// csv body
		foreach($output as $data){
			$csv .= API::array_to_csv($data) . "\r\r\n";;
		}

		// output the data
		return Response::make($csv, 200, static::$headers);
	}

	/**
	 * Return an XCRI-CAP feed for all programmes in a given year.
	 * 
	 * @param string $year year to generate xcri-cap of.
	 * @param string $level Either undergraduate or postgraduate.
	 * @return Response An XCRI-CAP field of the programmes for that year.
	 */
	public function get_xcri_cap($year)
	{
		// get last generated date
		$last_generated = API::get_last_change_time();
		
		// If cache is valid, send 304
		if($this->cache_still_valid($last_generated)){
			return Response::make('', '304');
		}

		// pull from cache or send a 404
		$cache_key = "xcri-cap-$year";
		$xcri = (Cache::has($cache_key)) ? Cache::get($cache_key) : false;

		if(!$xcri){
			return Response::make('', '404');
		}

		//atempt gzipping the feed
		$xcri = static::gzip($xcri);

		// set the content-type header
		static::$headers['Content-Type'] = 'text/xml';
		static::$headers['Last-Modified'] = API::get_last_change_date_for_headers($last_generated);
		
		//send xcri-cap as our response
		return Response::make($xcri, 200, static::$headers);
	}

	/**
	 * gzip the content if the request can handle gzipped content
	 *
	 * @param $content The string to gzip
	 * @return $content Hopefully gzipped
	 */
	public static function gzip($content)
	{
		// what do we have in our Accept-Encoding headers
		$HTTP_ACCEPT_ENCODING = isset($_SERVER["HTTP_ACCEPT_ENCODING"]) ? $_SERVER["HTTP_ACCEPT_ENCODING"] : ''; 
	    
		// set the right encoding
		if( headers_sent() ) 
	        $encoding = false; 
	    else if( strpos($HTTP_ACCEPT_ENCODING, 'x-gzip') !== false ) 
	        $encoding = 'x-gzip'; 
	    else if( strpos($HTTP_ACCEPT_ENCODING,'gzip') !== false ) 
	        $encoding = 'gzip'; 
	    else 
	        $encoding = false;
		
	    if($encoding){
			// Add the appropriate encoding header and gzip our content
	    	static::$headers['Content-Encoding'] = $encoding;
	    	$content = "\x1f\x8b\x08\x00\x00\x00\x00\x00" . gzcompress($content);
	    }

	    return $content;
	}
}
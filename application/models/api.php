<?php

class API {

	/**
	* Return the programmes index
	*
	* @param year year to get index for
	* @param level ug|pg
	* @return array Index of programmes
	*/
	public static function get_index($year, $level = false)
	{
		// Get index of programmes
		$level =  ($level === false) ? URLParams::get_type() : $level;
		if($year == 'current') $year = Setting::get_setting("{$level}_current_year");

		$model =  $level.'_Programme';
		return $model::get_api_index($year);
	}

	/**
	* get subjects index (hopefully from cache)
	*
	* @param $year
	* @param $level - ug or pg
	* @return array of subjects with couses attached.
	*/
	public static function get_subjects_index($year, $level = 'ug')
	{
		// api-output-ug gets wiped on every action.
		$level =  URLParams::get_type();
		$cache_key = "api-output-{$level}.subjects_index-$year";
		return (Cache::has($cache_key)) ? Cache::get($cache_key) : static::generate_subjects_index($year, $level);
	}

	/**
	* get subjects index from live(ish) data. Hopefully will hit internal caches for most of it.
	*
	* @param $year
	* @param $level - ug or pg
	* @return array of subjects with couses attached.
	*/
	public static function generate_subjects_index($year, $level = 'ug')
	{
		$level =  URLParams::get_type();
		// api-output-ug gets wiped on every action.
		$cache_key = "api-output-{$level}.subjects_index-$year";

		$model = $level.'_Programme';
		$subjects_model = $level.'_Subject';

		// Get subjects and course mappings
		$subjects = $subjects_model::get_api_data($year);
		$subjects_map = $model::get_api_related_programmes_map($year);
		// Generate feed array
		$subjects_array = array();
		foreach($subjects as $subject){
			$subject_item = $subject;
			$subject_item['courses'] = isset($subjects_map[$subject['id']]) ? $subjects_map[$subject['id']]  : array();
			$subjects_array[] = $subject_item;
		}

		// cache data
		Cache::put($cache_key, $subjects_array, 2628000);

		return $subjects_array;
	}

	/**
	* Return the fees index
	*
	* @param year year to get index for
	* @param level ug|pg
	* @return array Index of fees data
	*/
	public static function get_fees_index($year, $level = false, $fees_year = false)
	{
		// Get index of programmes
		$level =  ($level === false) ? URLParams::get_type() : $level;
		if($year == 'current') $year = Setting::get_setting("{$level}_current_year");
		$fees_year_field = GlobalSetting::get_fees_year_field();
		$fees_year = ($fees_year===false)? GlobalSetting::where('year','=',$year)->first(array($fees_year_field))->$fees_year_field:$fees_year;
		$model =  $level.'_Programme';
		return $model::get_api_fees($year,$fees_year);
	}

	/**
	* Return fully combined programme item from the API
	*
	* @param id ID of programme
	* @param year Year to get index for
	* @return combined Programme data array
	*/
	public static function get_programme($level, $year, $id)
	{

		if($year == 'current') $year = Setting::get_setting("{$level}_current_year");

		$cache_key = "api-output-{$level}.programme-$year-$id";
		return (Cache::has($cache_key)) ? Cache::get($cache_key) : static::generate_programme_data($level, $year, $id);
	}

	/**
	* generate fully combined programme item from caches
	*
	* @param id ID of programme
	* @param year year to get index for
	* @return combined programme data array
	*/

	public static function generate_programme_data($level, $year, $iid)
	{
		$cache_key = "api-output-{$level}.programme-$year-$iid";

		$prefix = API::_get_prefix($level);

		$settings_model = $prefix.'ProgrammeSetting';
		$programme_model = $prefix.'Programme';

		// Get basic data set
		$globals 			= GlobalSetting::get_api_data($year);
		$programme_settings = $settings_model::get_api_data($year);

		// Do we have the required data to show a programme?
		if($globals === false || $programme_settings === false){
			// Required data is missing.
			throw new MissingDataException("No published copy of programme/global data published for this year.");
		}

		// Get programme itself
		$programme 	= $programme_model::get_api_programme($iid, $year);

		// If programe does not exist/is not published.
		if($programme === false){
			throw new NotFoundException("Programme either does not exist or has not been published.");
		}

		$final = static::combine_programme($programme, $programme_settings, $globals, $level);

		$final['current_year'] = Setting::get_setting("{$level}_current_year");

		// combine statuses
		$statuses = '(';
		if($final['subject_to_approval'] == 'true'){
			$statuses .= "subject to approval";
		}
		if($final['programme_withdrawn'] == 'true'){
			$statuses .= $statuses == '(' ? "withdrawn" : ", withdrawn";
		}
		if ($final['programme_suspended'] == 'true') {
			$statuses .= $statuses == '(' ? "suspended" : ", suspended";
		}
		$statuses = $statuses == '(' ? '' : $statuses . ')';
		$final['programmme_status_text'] = $statuses;

		// Store data in to cache
		Cache::put($cache_key, $final, 2628000);

		return $final;
	}

	/**
	* get a "preview" programme from the API based on hash
	*
	* @param $hash of preview
	* @return preview from cache
	* @throws NoFoundException if preview does not exist (or has expired)
	*/
	public static function get_preview($hash)
	{
		$key = URLParams::get_type()."-programme-previews.preview-{$hash}";
		if(Cache::has($key)){
			return Cache::get($key);
		}else{
			throw new NotFoundException("Preview data not found");
		}
	}

	/**
	* Create a new "preview" of a given revision
	*
	* @param $id of programme preview is for
	* @param $id of revision to create preview from
	* @return hash of preview.
	*/
	public static function create_preview($id, $revision_id)
	{
		$model =  URLParams::get_type().'_Programme';
		$setting_model =  URLParams::get_type().'_ProgrammeSetting';

		$p = $model::find($id);
		$revision = $p->get_revision($revision_id);

		// If this revision exists
		if($revision !== false){
			// Grab additional data sets
			$globals 			= GlobalSetting::get_api_data($revision->year);
			$programme_settings = $setting_model::get_api_data($revision->year);
			// Fail if these arent set
			if($globals === false || $programme_settings === false){
				return false;
			}
			// Generate programme
			$final = static::combine_programme($revision->to_array(), $programme_settings, $globals);
			// Log revision identity
			$final['revision_id'] = $revision->get_identifier();

			// generate hash, use json_encode to make hashable (fastest encode method: http://stackoverflow.com/questions/804045/preferred-method-to-store-php-arrays-json-encode-vs-serialize )
			$hash = sha1(json_encode($final));
			// Store it, & return hash
			Cache::put(URLParams::get_type()."-programme-previews.preview-{$hash}", $final, 2419200);// 4 weeks
			return $hash;
		}

		return false;
	}

	/**
	* Get "data" from specific datatype
	*
	* @param $type data type to return data for.
	* @throws NotFoundException on unknown datatype
	* @return array of data to return.
	*/
	public static function get_data($type, $level = null){
		// Do some magic (ie. convert schools=>school & campuses to campus for models)
		$pluralizer = new \Laravel\Pluralizer(Config::get('strings'));
		$type = $pluralizer->singular($type);

		$prefix = API::_get_prefix($level);

		// If type exists, return data
		if(class_exists($prefix.$type)){
			$type = $prefix.$type;
			return $type::get_api_data();
		}
		// Else throw 404
		throw new NotFoundException("Request for unknown data type.");
	}

	/**
	* Create a combined programme output
	*
	* @param $programme - basic programme data
	* @param $programme_settings - basic programme setting data
	* @param $globals - basic global setting data
	* @return Combined programme data (fully linked)
	*/
	public static function combine_programme($programme, $programme_settings, $globals, $level = false){
		// Get Models
		$programme_model = URLParams::get_type().'_Programme';
		$awards_model = URLParams::get_type().'_Award';

		// Initialise the level
		$level = ($level == false) ? URLParams::get_type() : $level;

		// Start combining to create final super object for output
		// Use programme setttings as a base
		$final = $programme_settings;

		// Pull in all programme dependencies eg an award id 1 will pull in all that award's data.
		// Loop through them, adding them to the $final object.
		$programme = API::load_external_data($programme, $level);

		// Add in all values from main programme
		// Only overwrite values previously added from "settings" when they are not blank
		foreach($programme as $key => $value)
		{
			// Make sure any existing key in the $final object gets updated with the new $value.
			if(!empty($value) ){
				$final[$key] = $value;
			}
		}

		// Remove unwanted attributes
		foreach(array('id','global_setting_id', 'programme_id', 'programme_setting_id') as $key)
		{
			unset($final[$key]);
		}

		// Now remove IDs from our field names, they're not necessary and return.
		// e.g. 'programme_title_1' simply becomes 'programme_title'.
		$final = static::remove_ids_from_field_names($final);

		// Apply related courses
		if(empty($final['subject_area_1'])){
			throw new MissingMagicalUnicornFieldException('subject_area_1 should never be set null');
		}

		$subject_area_1 = $final['subject_area_1'][0]['id'];

		// Get subject area two if its set
		$subject_area_2 = null;

		if(!empty($final['subject_area_2'])){
			$subject_area_2 = $final['subject_area_2'][0]['id'];
		}

		// Related courses
		$related_courses = $programme_model::get_programmes_in($subject_area_1, $subject_area_2, $programme['year'], $programme['instance_id']);
		$final['related_courses'] = static::merge_related_courses($related_courses, $final['related_courses']);

		// Add global settings data
		$final['globals'] = static::remove_ids_from_field_names($globals);

		$final['programme_level'] = $level;

		// Add in deliveries data
		$final['deliveries'] = $programme_model::find_deliveries($final['instance_id'], $final['year']);
		$modules = array();

		foreach($final['deliveries'] as &$delivery){
			$delivery_awards = $awards_model::replace_ids_with_values($delivery['award'], false, true);
			$delivery['award_name'] = isset($delivery_awards[0]) ? $delivery_awards[0] : '';

			// Add fee data
			$delivery['fees'] = Fees::getFeeInfoForPos($delivery['pos_code'], $final['globals']['fees_year']);

			// Add modules
			$modules[] = API::get_module_data($programme['instance_id'], $delivery['pos_code'], $programme['year'], $level);
		}

		if($final['module_session']=='None' || $final['module_session'] == 'none'){
			$final['modules'] = array();
		}else{
			$final['modules'] = $modules;
		}


		return $final;
	}

	/**
	* Merge related courses. Merges course arrays removing any duplicates and returns them in alphabetical order
	*
	* @param $related_courses, Inital array of courses
	* @param $additional_related_courses, Additional courses array of courses
	*
	* @return (array) $related_courses
	*/
	public static function merge_related_courses($related_courses, $additional_related_courses){
		// Merge arrays (copying over duplicates)
		if(is_array($additional_related_courses)){
			foreach($additional_related_courses as $course){
				// If course doesnt exist in generated array, add it.
				if(!isset($related_courses[$course['id']])) $related_courses[$course['id']] = $course;
			}
		}

		// Make alphabetical
		if(isset($related_courses) && !empty($related_courses)){
			usort($related_courses, function($a,$b){
				return strcmp($a["name"], $b["name"]);
			});
		}


		return $related_courses;
	}


	/**
	* Get Module Data
	*
	* @param id ID of programme to get module data for
	* @param year year of progamme
	* @param type of progamme ug|pg
	*
	* @return Module data | false
	*/
	public static function get_module_data($iid, $pos, $year, $level = 'ug')
	{
		$cache_key = "programme-modules.$level-$year-".base64_encode($pos)."-$iid";
		return (Cache::has($cache_key)) ? Cache::get($cache_key) : false;
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
	* look through the passed in record and substitute any ids with data from the correct table
	*
	* @param $record The record
	* @return $new_record A new record with ids substituted
	*/
	public static function load_external_data($record, $level = false)
	{
		$level = ($level == false) ? URLParams::get_type() : $level;
		$field_model = $level.'_ProgrammeField';
		// get programme fields (mapping of columns to their datatypes)
		$programme_fields = $field_model::get_api_data();

		// For each column with a special data type, update its value in the record;
		foreach($programme_fields as $field_name => $data_type){
			$record[$field_name] = $data_type::replace_ids_with_values($record[$field_name], $record['year']);
		}

		return $record;
	}

	/**
	* Purge output cache. Clears final output caches when data is changed.
	*
	* @param $data Data to show as XML
	* @return Raw XML
	*/
	public static function purge_output_cache()
	{
		// @todo work out a way of purging this data in tests
		// so we can test logic creates/removes the correct files
		if(Request::env() != 'test'){

			//separate trys as will throw exception if dir does not exist.

			// Pokémon expection handling, gotta catch em all.
			try {
				Cache::purge('api-output-ug');
			}catch (Exception $e) {
				// Do nothing, all this means if there was no directory (yet) to wipe
			}
			// Pokémon expection handling, gotta catch em all.
			try {
				Cache::purge('api-output-pg');
			}catch (Exception $e) {
				// Do nothing, all this means if there was no directory (yet) to wipe
			}

		}

		// Last changed timestamp
		// (All data generated after this point can be considered as "up to date" by the API)
		Cache::put('last_change', time(), 2628000);
	}

	public static function purge_fees_cache($fees_year = false)
	{
		$pg_key = 'api-index-pg.fees';
		$ug_key = 'api-index-ug.fees';
		if(!empty($fees_year)){
			$pg_key .='.' . $fees_year;
			$ug_key .='.' . $fees_year;
		}
		// Pokémon expection handling, gotta catch em all.
		try {

			Cache::purge($pg_key);
		}catch (Exception $e) {
			// Do nothing, all this means if there was no directory (yet) to wipe
		}
		// Pokémon expection handling, gotta catch em all.
		try {
			Cache::purge($ug_key);
		}catch (Exception $e) {
			// Do nothing, all this means if there was no directory (yet) to wipe
		}
		// Last changed timestamp
		// (All data generated after this point can be considered as "up to date" by the API)
		Cache::put('last_change', time(), 2628000);
	}

	/**
	* Get timestamp of when last change to caches was made
	*
	* @return unix timestamp
	*/
	public static function get_last_change_time(){
		// Get last change
		$last_change = Cache::get('last_change');
		// If this is the first, say its now
		if($last_change == null){
			Cache::put('last_change', time(), 2628000);
			$last_change = time();
		}
		return $last_change ;
	}

	/**
	* Get last change time in format sutable for use in headers
	*
	* @param timestamp to generate header from
	* @return time formatted for header
	*/
	public static function get_last_change_date_for_headers($time = false){

		if($time){
			// if time was provided, dont grab from cache
			$last_change = $time;
		}else{
			$last_change = static::get_last_change_time();
		}
		// else return in correct format
		return gmdate('D, d M Y H:i:s \G\M\T', $last_change);
	}

	/**
	* Function to convert feed to XML
	*
	* @param $data Data to show as XML
	* @return Raw XML
	*/
	public static function array_to_xml($data, $xml = false)
	{

		if ($xml === false)
		{
			$xml  = new SimpleXMLElement('<?xml version="1.0" encoding="'.Config::get('application.encoding').'"?><response/>');
		}

		foreach($data as $key => $value)
		{
			if(is_int($key)){
				$key = 'item'; // Else will use 1/2/3/etc which is invalid xml
			}else{
				$key = preg_replace('~\W*~', "", $key);// Remove any funny chars from keys
			}

			if (is_array($value) || is_object($value))
			{
				static::array_to_xml($value, $xml->addChild($key));
			}
			else
			{
				$xml->addChild($key, str_replace('&', '&amp;', $value));
			}
		}
		// Decode &chars; in XMl to ensure its valid.
		return $xml->asXML();
	}

	/**
	* Function to convert feed to CSV
	*
	* @see http://stackoverflow.com/questions/3933668/convert-array-into-csv
	* @param $data Data to show as CSV
	* @return Raw CSV
	*/
	public static function array_to_csv( array &$fields, $delimiter = ',', $enclosure = '"', $encloseAll = false, $nullToMysqlNull = false ) {
		$delimiter_esc = preg_quote($delimiter, '/');
		$enclosure_esc = preg_quote($enclosure, '/');

		$output = array();
		foreach ( $fields as $field ) {
			if ($field === null && $nullToMysqlNull) {
				$output[] = 'NULL';
				continue;
			}
			if(is_array($field) && !empty($field)){
				if(!is_array(current($field))){
					$field = implode(', ',$field);
				}else{
					$field = '## nested data  ##';
				}
			}else{
				if(empty($field)){
					$field ='';
				}
				// trim
				$field = trim($field);
			}
			// Enclose fields containing $delimiter, $enclosure or whitespace
			if ( $encloseAll || preg_match( "/(?:${delimiter_esc}|${enclosure_esc}|\s)/", $field ) ) {
				$output[] = $enclosure . str_replace($enclosure, $enclosure . $enclosure, $field) . $enclosure;
			}
			else {
				$output[] = $field;
			}
		}

		return implode( $delimiter, $output );
	}

	/**
	* Creates a flat representation of a programme for use in XCRI.
	*
	* @return StdClass A flattened and simplified XCRI ready representation of this object.
	*/
	public static function get_xcrified_programme($id, $year, $type = false)
	{
		// get the programme
		$programme = static::get_programme($type, $year, $id);

		// format the programme appropriately
		$ug_pg_full = (strcmp($type, 'ug') == 0) ? 'undergraduate' : 'postgraduate';
		$programme['url'] = Config::get('application.front_end_url') . $ug_pg_full . '/' . $id . '/' . $programme['slug'];
		$programme['award'] = isset($programme['award'][0]) ? $programme['award'] : array();
		$programme['administrative_school'] = isset($programme['administrative_school'][0]) ? $programme['administrative_school'][0] : array();
		$programme['additional_school'] = isset($programme['additional_school'][0]) ? $programme['additional_school'][0] : array();
		$programme['subject_area_1'] = isset($programme['subject_area_1'][0]) ? $programme['subject_area_1'][0] : array();
		$programme['subject_area_2'] = isset($programme['subject_area_2'][0]) ? $programme['subject_area_2'][0] : array();
		$programme['location'] = isset($programme['location'][0]) ? $programme['location'][0] : array();
		$programme['subject_leaflet'] = isset($programme['subject_leaflet'][0]) ? $programme['subject_leaflet'][0] : array();
		$programme['subject_leaflet_2'] = isset($programme['subject_leaflet_2'][0]) ? $programme['subject_leaflet_2'][0] : array();

		// merge subjects areas into one array
		if(!empty($programme['subject_area_1'])){
			$programme['subjects'][] = $programme['subject_area_1'];
		}

		if(!empty($programme['subject_area_2'])){
			$programme['subjects'][] = $programme['subject_area_2'];
		}

		// for fees display
		$programme['has_parttime'] = (strpos(strtolower($programme['mode_of_study']), 'part-time') !== false);
		$programme['has_fulltime'] = (strpos(strtolower($programme['mode_of_study']), 'full-time') !== false);

		$programme['modes_of_study'] = array();

		if ($programme['has_fulltime'] || $programme['has_parttime']) {
				if ($programme['has_fulltime']) {
						$programme['modes_of_study'][] = array('id' => 'FT', 'name' => "Full time");
				}
				if ($programme['has_parttime']) {
						$programme['modes_of_study'][] = array('id' => 'PT', 'name' => "Part time");
				}
		} else {
				$programme['modes_of_study'][]['name'] = $programme['mode_of_study'];
				if (stristr($programme['mode_of_study'], 'full')) {
						$programme['modes_of_study'][]['id'] = 'FT';
				} else if (stristr($programme['mode_of_study'], 'part')) {
						$programme['modes_of_study'][]['id'] = 'PT';
				}
		}
		
		// attendance mode
		if (strpos($programme['attendance_mode'], 'Mixed') !== false){
			$programme['attendance_mode_id'] = 'MM';
			$programme['attendance_mode'] = 'Mixed mode';
		}
		elseif (strpos($programme['attendance_mode'], 'Distance with attendance') !== false){
			$programme['attendance_mode_id'] = 'DA';
			$programme['attendance_mode'] = 'Distance with attendance';
		}
		elseif (strpos($programme['attendance_mode'], 'Distance without attendance') !== false){
			$programme['attendance_mode_id'] = 'DS';
			$programme['attendance_mode'] = 'Distance without attendance';
		}
		elseif (strpos($programme['attendance_mode'], 'Campus') !== false){
			$programme['attendance_mode_id'] = 'CM';
			$programme['attendance_mode'] = 'Campus';
		}
		else{
			$programme['attendance_mode_id'] = 'CM';
			$programme['attendance_mode'] = 'Campus';
		}

		// transform attendence_pattern ad needed.
		$programme['attendance_pattern'] = strtolower($programme['attendance_pattern']);
		switch ($programme['attendance_pattern']) {
			case 'day-time':
			$programme['attendance_pattern'] = 'Daytime';
			$programme['attendance_pattern_id'] = 'DT';
			break;
			case 'weekend':
			$programme['attendance_pattern'] = 'Weekend';
			$programme['attendance_pattern_id'] = 'WE';
			break;
			case 'evening':
			$programme['attendance_pattern'] = 'Evening';
			$programme['attendance_pattern_id'] = 'EV';
			break;
			case 'customized':
			$programme['attendance_pattern'] = 'Customised';
			$programme['attendance_pattern_id'] = 'CS';
			break;
			default:
			$programme['attendance_pattern'] = 'Daytime';
			$programme['attendance_pattern_id'] = 'DT';
		}

		// set the duration
		if (isset($programme['duration']) && !empty($programme['duration']) && is_int(intval($programme['duration']))) {
			$programme['duration_text_id'] = 'P'.$programme['duration'].'M';
			$programme['duration_text'] = $programme['duration'].' months';
		}
		else {
			$programme['duration_text_id'] = 'P12M';
			$programme['duration_text'] = '12 months';
		}

		// start date
		if (isset($programme['start_date']) && !empty($programme['start_date'])) {
			$programme['start_date_text'] = date('M Y', strtotime($programme['start_date']));
		}
		else {
			$programme['start_date'] = $programme['year'] . '-09';
			$programme['start_date_text'] = date('M Y', strtotime($programme['start_date']));
		}

		// pull out the school enquiry details
		$enq = strip_tags($programme['enquiries']);

		$enquiry_phone = explode("\n", strstr($enq, 'T:'));
		$programme['enquiry_phone'] = isset($enquiry_phone[0]) ? trim(substr($enquiry_phone[0], 2)) : '';

		$enquiry_email = explode("\n", strstr($enq, 'E:'));
		$programme['enquiry_email'] = isset($enquiry_email[0]) ? trim(substr($enquiry_email[0], 2)) : '';

		$enquiry_fax = explode("\n", strstr($enq, 'F:'));
		$programme['enquiry_fax'] = isset($enquiry_fax[0]) ? trim(substr($enquiry_fax[0], 2)) : '';


		// Leave as is for the moment.
		$programme['cost'] = (strcmp($type, 'ug') == 0) ? $programme['tuition_fees'] : $programme['fees_and_funding'];

		// Set the programme type
		$programme['type'] = $type;

		return $programme;
	}

	/**
	* Return programme model of correct type
	*
	* @return UG/PG_Programme
	*/
	public static function get_programme_model(){
		return static::_get_prefix(URLParams::get_type()).'Programme';
	}

	/**
	* Get correct prefix for model
	*
	* @param $level
	* @return "UG_" / "PG_"
	*/
	public static function _get_prefix($level){
		switch($level){
			case 'ug':
			$prefix = 'UG_';
			break;
			case 'pg':
			$prefix = 'PG_';
			break;
			default:
			$prefix = '';
			break;
		}

		return $prefix;
	}
}

// Exceptions
class MissingMagicalUnicornFieldException extends \Exception {}
	class MissingDataException extends \Exception {}
		class NotFoundException extends \Exception {}

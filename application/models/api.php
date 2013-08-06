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
	 * Return fully combined programme item from the API
	 *
	 * @param id ID of programme
	 * @param year Year to get index for
	 * @return combined Programme data array
	 */
	public static function get_programme($level, $year, $id)
	{
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

	//	$final['deliveries'] = static::attach_pg_deliveries($iid, $year);

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

		$programme_model = URLParams::get_type().'_Programme';

		$related_courses = $programme_model::get_programmes_in($subject_area_1, $subject_area_2, $programme['year'], $programme['instance_id']);

		$final['related_courses'] = static::merge_related_courses($related_courses, $final['related_courses']);

		// Add global settings data
		$final['globals'] = static::remove_ids_from_field_names($globals);
		
		$final['programme_level'] = $level;



		// Add deliveries if PG, Then use to grab modules
		if($level == 'pg'){
			// only get if has a programme_type and the type includes the string taught
			if( isset($final['programme_type']) && (strpos($final['programme_type'], 'taught') !== false)){

				$final['deliveries'] = PG_Deliveries::get_programme_deliveries($final['instance_id'], $final['year']);
				// get modules
				$modules = array();
				foreach($final['deliveries'] as &$delivery){
					$delivery_awards = PG_Award::replace_ids_with_values($delivery['award'],false,true);
					$delivery['award_name'] = $delivery_awards[0];
					$modules[] = API::get_module_data($programme['instance_id'], $delivery['pos_code'], $programme['year'], $level);
				}
				if(sizeof($modules) != 0) $final['modules'] = $modules;

			}

		}
		else
		{ 
			// if UG, grab modules normally
			$modules = API::get_module_data($final['instance_id'], $final['pos_code'], $final['year'], $level);
			if($modules !== false)$final['modules'] = $modules;
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
		usort($related_courses, function($a,$b){
			 return strcmp($a["name"], $b["name"]);
		});

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
			// Pokémon expection handling, gotta catch em all.
			try {
				Cache::purge('api-output-ug');
				Cache::purge('api-output-pg');
			}catch (Exception $e) {
				// Do nothing, all this means if there was no directory (yet) to wipe
			}
			
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
	 * Creates a flat representation of a programme for use in XCRI.
	 * 
	 * @return StdClass A flattened and simplified XCRI ready representation of this object.
	 */
	public static function get_xcrified_programme($id, $year, $type = false)
	{
		// get the programme
		$programme = static::get_programme($type, $year, $id);
	
		// format the programme appropriately
		$programme['url'] = Config::get('application.front_end_url') . 'undergraduate/' . $id . '/' . $programme['slug'];

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

		// Set campus as default while we are making a dummy.
		$programme['attendance_mode_id'] = 'CM';
		$programme['attendance_mode'] = 'Campus';

		// Also dummy attendence_pattern_id for now. Set to daytime.
		$programme['attendance_pattern'] = 'Daytime';
		$programme['attendance_pattern_id'] = 'DT';

		// Leave as is for the moment.
		$programme['cost'] = (strcmp($type, 'ug') == 0) ? $programme['tuition_fees'] : $programme['fees_and_funding'];

		// Set the programme type
		$programme['type'] = $type;

		return $programme;
	}



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

class MissingMagicalUnicornFieldException extends \Exception {}
class MissingDataException extends \Exception {}
class NotFoundException extends \Exception {}
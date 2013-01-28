<?php

class API {
	
	/**
	 * Return the programmes index
	 *
	 * @param year year to get index for
	 * @param level ug|pg
	 * @return array Index of programmes
	 */
	public static function get_index($year, $level = 'ug')
	{
		// Get index of programmes
		return Programme::get_api_index($year);
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
		$cache_key = "api-output-ug.subjects_index-$year";
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
		// api-output-ug gets wiped on every action.
		$cache_key = "api-output-ug.subjects_index-$year";

		// Get subjects and course mappings
		$subjects = Subject::get_api_data($year);
		$subjects_map = Programme::get_api_related_programmes_map($year);
		// Generate feed array
		$subjects_array = array();
		foreach($subjects as $subject){
			$subject_item = $subject;
			$subject_item['courses'] = $subjects_map[$subject['id']];
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
	 * @param year year to get index for
	 * @return combined programme data array
	 */
	public static function get_programme($id, $year)
	{
		$cache_key = "api-output-ug.programme-$year-$id";
		return (Cache::has($cache_key)) ? Cache::get($cache_key) : static::generate_programme_data($id, $year);
	}

	/**
	 * generate fully combined programme item from caches
	 *
	 * @param id ID of programme
	 * @param year year to get index for
	 * @return combined programme data array
	 */
	public static function generate_programme_data($id, $year)
	{
		$cache_key = "api-output-ug.programme-$year-$id";

		// Get basic data set
		$globals 			= GlobalSetting::get_api_data($year);	
		$programme_settings = ProgrammeSetting::get_api_data($year);
		
		// Do we have the required data to show a programme?
		if($globals === false || $programme_settings === false){
			// Required data is missing.
			throw new MissingDataException("No published copy of programme/global data published for this year.");
		}

		// Get programme itself
		$programme 	= Programme::get_api_programme($id, $year);

		// If programe does not exist/is not published.
		if($programme === false){
			throw new NotFoundException("Programme either does not exist or has not been published.");
		}

		// Start combining to create final super object for output
		// Use programme setttings as a base
		$final = $programme_settings;

		// Pull in all programme dependencies eg an award id 1 will pull in all that award's data.
		// Loop through them, adding them to the $final object.
		$programme = API::load_external_data($programme);

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
		foreach(array('id','global_setting_id') as $key)
		{
			unset($final[$key]);
		}
		
		// Now remove IDs from our field names, they're not necessary and return.
		// e.g. 'programme_title_1' simply becomes 'programme_title'.
		$final = static::remove_ids_from_field_names($final);

		// Apply related courses
		$related_courses = Programme::get_courses_in($final['subject_area_1'][0]['id'], $final['subject_area_2'][0]['id'], $year, $id);
		$final['related_courses'] = static::merge_related_courses($related_courses, $final['related_courses']);

		// Add global settings data
		$final['globals'] = static::remove_ids_from_field_names($globals);

		// Finally, try and add some module data
		$modules = API::get_module_data($id, $year);
		if($modules !== false){
			$final['modules'] = $modules;
		}

		// Store data in to cache
		Cache::put($cache_key, $final, 2628000);

		return $final;
	}


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
	public static function get_module_data($id, $year, $level = 'ug')
	{
		$cache_key = "programme-modules.$level-$year-$id";
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
	public static function load_external_data($record)
	{
		// get programme fields (mapping of columns to their datatypes)
		$programme_fields =  ProgrammeField::get_api_data();

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
			}catch (Exception $e) {
				// Do nothing, all this means if there was no directory (yet) to wipe
			}
			
		}
		
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
				$xml->addChild($key, $value);
			}
		}
		// Decode &chars; in XMl to ensure its valid.
		return str_replace('&','&amp;',$xml->asXML());
	}

}

class MissingDataException extends \Exception {}
class NotFoundException extends \Exception {}
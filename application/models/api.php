<?php

class API {
	
	// Get index (for search)
	public static function get_index($year, $level = 'ug'){
		// Get index of programmes
		return Programme::get_api_index($year);
	}

	// Get a fully combined programme 
	public static function get_programme($id, $year){

		// Get basic data set
		$globals 				= GlobalSetting::get_api_data($year);	
		$programme_settings 	= ProgrammeSetting::get_api_data($year);
		$programme 				= Programme::get_api_programme($id, $year);

		if($globals===false || $programme_settings===false){
			// Error A: No live versions of globals or settings
			// Maybe throw exception here, or return status code?
			return false;
		}
		if($programme === false){
			// Error B: Programme not published.
			// Maybe throw exception here, or return status code?
			return false;
		}

		// Start combineing to create final super object for output
		// Use globals as base
		$final = $globals;
		
		// Then add in values from settings
		foreach($programme_settings as $key => $value)
		{
			$final->{$key} = $value;
		}

		// Pull in all programme dependencies eg an award id 1 will pull in all that award's data.
		// Loop through them, adding them to the $final object.
		$programme = Programme::pull_external_data($programme);

		// Add in all values from main programme
		// Only overwrite values previously added from "settings" when they are not blank
		foreach($programme as $key => $value)
		{
			// Make sure any existing key in the $final object gets updated with the new $value.
			if(!empty($value) ){
				$final->{$key} = $value;
			}
		}

		// Remove unwanted attributes
		foreach(array('id','global_setting_id') as $key)
		{
			unset($final->{$key});
		}
		
		// Now remove IDs from our field names, they're not necessary and return.
		// e.g. 'programme_title_1' simply becomes 'programme_title'.
		return static::remove_ids_from_field_names($final);;
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




}
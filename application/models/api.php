<?php

class API {
	
	// Get index (for search)
	public static function get_index($year){
		return Programme::get_api_index($year);
	}

	// Get a fully combined programme 
	public static function get_programme($id, $year){


		$globals 				= GlobalSettings::get_api_programme($id, $year);	
		$programme_settings 	= ProgrammeSettings::get_api_programme($id, $year);
		$programme 				= Programme::get_api_programme($id, $year);

		$final = $globals;
		
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


		return Programme::get_api_index();


	}




}
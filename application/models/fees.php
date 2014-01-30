<?php
/**
 * Fees Object
 *
 */
class Fees {

	/**
	 * getFeeInfoForPos - returns fee data object for given pos code
	 * 
	 * @param $pos code
	 * @param $year
	 *
	 * @return Fee Data array | false
	 */
	public static function getFeeInfoForPos($pos, $year){
		// Load fee mapping, and if fees exist return them, else false
		$fees = static::get_fee_mapping($year);
		return (isset($fees[$pos])) ? $fees[$pos] : false;
	}

	/**
	 * get_fee_mapping - Gets cached lookup object, for quickling getting fee data from POS.
	 * 
	 * @param $year
	 *
	 * @return Fee Data array
	 */
	public static $mapping = false;
	public static function get_fee_mapping($year){

		// If loaded in memory, use that. (PG courses often have multiple instances of the same POS's)
		if(static::$mapping) return static::$mapping;

		// Else, load object from cache
		return static::$mapping = Cache::get("fee-mappings-{$year}", function() use ($year){ 

			$path = Config::get('fees.path');

			// If no cache, open up feedbands and mapping csv files for given year
			$fees = Fees::load_csv("{$path}/{$year}-feebands.csv");
			$courses = Fees::load_csv("{$path}/{$year}-mapping.csv");

			// Ensure data was found
			if(!$fees || !$courses) return array();

			// Map fees to speed up lookups
			$fee_map = array();
			foreach($fees as $feeband){
				$fee_map[$feeband["band"]] = $feeband;
			}

			// Map rest of for efficant lookups
			$mapping = array();
			foreach($courses as $course){

				$mapping[$course['Pos Code']] = array(
						'home' => isset($fee_map[$course["UK/EU Fee Band"]]) ? $fee_map[$course["UK/EU Fee Band"]] : false ,
						'int' => isset($fee_map[$course["Int Fee Band"]]) ? $fee_map[$course["Int Fee Band"]] :false
					);
			}
			// return data to cache
			return $mapping;

		}, 172800); // 48 hour cache
	}

	/**
	 * load_csv - Loads a csv file, and converts data to array
	 * 
	 * @param $filename
	 * @return array
	 */
	public static function load_csv($filename)
	{
	    if(!file_exists($filename) || !is_readable($filename))
	        return FALSE;

	    $header = NULL;
	    $data = array();
	    if (($handle = fopen($filename, 'r')) !== FALSE)
	    {
	        while (($row = fgetcsv($handle, 1000)) !== FALSE)
	        {
	            if(!$header) {
	                $header = $row;
	            }
	            elseif ( count($header) == count($row) ) {
	                $data[] = array_combine($header, $row);
	            }
	        }
	        fclose($handle);
	    }
	    return $data;
	}

}
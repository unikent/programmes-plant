<?php
/**
 * Fees Object
 *
 */
class Fees {

	// In memory cache of fee data structure 
	public static $mapping = false;

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
	 * get_fee_mapping - Gets cached lookup object, for quickly getting fee data from POS.
	 * 
	 * @param $year
	 *
	 * @return Fee Data array
	 */
	public static function get_fee_mapping($year){

		// If loaded in memory, use that. (PG courses often have multiple instances of the same POS's)
		if(static::$mapping) return static::$mapping;

		// Else, load object from cache
		return static::$mapping = Cache::get("fee-mappings-{$year}", static::generate_fee_map($year) ); // 48 hour cache
	}

	/**
	 * generate_fee_map - Create fee mapping data & shove it in a cache
	 * 
	 * @param $year
	 * @return Fee Data array
	 */
	public static function generate_fee_map($year){

		$path = Config::get('fees.path');

		// If no cache, open up feedbands and mapping csv files for given year
		$fees = Fees::load_csv_from_webservice("{$path}/{$year}-feebands.csv");
		$courses = Fees::load_csv_from_webservice("{$path}/{$year}-mapping.csv");

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

		// Cache it
		Cache::forever("fee-mappings-{$year}", $mapping);

		return $mapping;
	}

	/**
	 * Load csv from web service & parse to usable array
	 *
	 * @param $url
	 * @return array | false
	 */
	public static function load_csv_from_webservice($url){
		// Try web service
		$raw = file_get_contents($url);
		if($raw === false) return false;

		$data = array();
		$header = NULL;

		$lines = explode(PHP_EOL, $raw);
		// for each line, parse as csv and add to data
		// first line = headings
		// rest get array_combined to be heading=>value
		foreach($lines as $line){
			$row =str_getcsv($line);
			if(!$header) {
				$header = $row;
			}
			elseif ( count($header) == count($row) ) {
				$data[] = array_combine($header, $row);
			}
		}

		return $data;
	}

}
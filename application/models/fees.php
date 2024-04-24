<?php

/**
 * Fees Object
 *
 */
class Fees {

	// In memory cache of fee data structure 
	public static $mapping = array();

	/**
	 * getFeeInfoForPos - returns fee data object for given pos code
	 * 
	 * @param $pos code
	 * @param $fee_year year to look at for fees data (may not be programme year) see fees_year global
	 *
	 * @return Fee Data array | false
	 */
	public static function getFeeInfoForPos($pos, $fee_year){
		//trim just in case extra spaces from SITS
		$pos = trim($pos);
		// Load fee mapping, and if fees exist return them, else false
		$fees = static::get_fee_mapping($fee_year);
		return (isset($fees[$pos])) ? $fees[$pos] : false;
	}

	/**
	 * getCondensedFeeInfoForPos - returns fee data object for given pos code in a condensed format
	 * ie:
	 *	"currency": "pound",
	 * 	"home_full_time": "N/A",
	 *	"home_part_time": "TBC",
	 *	"int_full_time": "14000",
	 *	"int_part_time": "7020"
	 * 
	 * @param $pos code
	 * @param $fee_year year to look at for fees data (may not be programme year) see fees_year global
	 *
	 * @return Fee Data array | false
	 */
	public static function getCondensedFeeInfoForPos($pos, $fee_year){

		$fee = static::getFeeInfoForPos($pos, $fee_year);
		if (empty($fee)) {
			return false;
		}

		$currency = 'pound';
		$fee_amount_prefix = '';
		if (!empty($fee['home']['euro-full-time']) || !empty($fee['home']['euro-part-time'])) {
			$currency = 'euro';
			$fee_amount_prefix = 'euro-';
		}

		return array(
			'currency'			=>		$currency,
			'home_full_time'	=>		empty($fee['home']) || empty($fee['home'][$fee_amount_prefix . 'full-time']) ? 'TBC' : (strtolower(str_replace('/', '', $fee['home'][$fee_amount_prefix . 'full-time'])) == 'na' ? 'N/A' : $fee['home'][$fee_amount_prefix . 'full-time']),
			'home_part_time'	=>		empty($fee['home']) || empty($fee['home'][$fee_amount_prefix . 'part-time']) ? 'TBC' : (strtolower(str_replace('/', '', $fee['home'][$fee_amount_prefix . 'part-time'])) == 'na' ? 'N/A' : $fee['home'][$fee_amount_prefix . 'part-time']),
			'int_full_time'		=>		empty($fee['int']) || empty($fee['int'][$fee_amount_prefix . 'full-time']) ? 'TBC' : (strtolower(str_replace('/', '', $fee['int'][$fee_amount_prefix . 'full-time'])) == 'na' ? 'N/A' : $fee['int'][$fee_amount_prefix . 'full-time']),
			'int_part_time'		=>		empty($fee['int']) || empty($fee['int'][$fee_amount_prefix . 'part-time']) ? 'TBC' : (strtolower(str_replace('/', '', $fee['int'][$fee_amount_prefix . 'part-time'])) == 'na' ? 'N/A' : $fee['int'][$fee_amount_prefix . 'part-time']),
			'eu_full_time'		=>		empty($fee['eu']) || empty($fee['eu'][$fee_amount_prefix . 'full-time']) ? 'TBC' : (strtolower(str_replace('/', '', $fee['eu'][$fee_amount_prefix . 'full-time'])) == 'na' ? 'N/A' : $fee['eu'][$fee_amount_prefix . 'full-time']),
			'eu_part_time'		=>		empty($fee['eu']) || empty($fee['eu'][$fee_amount_prefix . 'part-time']) ? 'TBC' : (strtolower(str_replace('/', '', $fee['eu'][$fee_amount_prefix . 'part-time'])) == 'na' ? 'N/A' : $fee['eu'][$fee_amount_prefix . 'part-time'])
		);
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
		if(isset(static::$mapping[$year])) return static::$mapping[$year];

		// Else, load object from cache
		return static::$mapping[$year] = Cache::get("fee-mappings-{$year}", function() use ($year) { return Fees::generate_fee_map($year, false); });
	}

	/**
	 * generate_fee_map - Create fee mapping data & shove it in a cache
	 * 
	 * @param $year
	 * @return array | bool
	 *					- array of fee data,
	 *					- empty array if no fee data found
	 *                  - true if fees unchanged
	 * 					- false if fees config path is not configured
	 */
	public static function generate_fee_map($year, $cache_exists = true){

		$path = Config::get('fees.path');
		if($path == '') return false;


		if ($year ==='preview'){
			$path = explode('/',$path);

			array_pop($path);

			$path = implode('/',$path) . '/preview-fees';

			// If no cache, open up feedbands and mapping csv files for preview
			$fees = Fees::load_csv_from_webservice("{$path}/preview-feebands.csv");
			$courses = Fees::load_csv_from_webservice("{$path}/preview-mapping.csv");
		}else {
			// If no cache, open up feedbands and mapping csv files for given year
			$fees = Fees::load_csv_from_webservice("{$path}/{$year}-feebands.csv");
			$courses = Fees::load_csv_from_webservice("{$path}/{$year}-mapping.csv");
			
		}

		// Ensure data was found
		if(!$fees || !$courses || empty($fees) || empty($courses)) return array();

		// Ensure data has actually changed (and its worth continuing (No point busting all our caches if we don't need to)
		$mapping_hash_cache = "fee-mapping-hash-{$year}";

		$unique_datahash = md5(json_encode($fees).json_encode($courses));
		$old_datahash = Cache::get($mapping_hash_cache);

		// If hash's match, data is the same, so theres no point in regenerating the file
		// That said, if the cached data is broken/missing (cache_exists = false) we need to generate anyway
		if($cache_exists && $unique_datahash == $old_datahash) return true;

		// Update cache with new udh (unique data hash)
		Cache::forever($mapping_hash_cache, $unique_datahash);

		//
		// Actually regenerate data (if we got this far, we need too.)
		//

		// Map fees to speed up lookups
		$fee_map = array();
		foreach($fees as $feeband){
			$fee_map[$feeband["band"]] = $feeband;
		}

		// Map rest of for efficient lookups
		$mapping = array();
		foreach($courses as $course){

			$mapping[$course['Pos Code']] = array(
					'home' => isset($fee_map[$course["UK Fee Band"]]) ? $fee_map[$course["UK Fee Band"]] : false ,
					'int' => isset($fee_map[$course["Int Fee Band"]]) ? $fee_map[$course["Int Fee Band"]] :false,
					'eu' => isset($fee_map[$course["EU Fee Band"]]) ? $fee_map[$course["EU Fee Band"]] :false
				);
		}	

		// Cache it
		Cache::forever("fee-mappings-{$year}", $mapping);

		// Flush output caches, so new data is reflected
		try
		{
			API::purge_fees_cache($year);
			API::purge_output_cache();
		}
		catch(Exception $e)
		{

		}

		// return data
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
		$raw = @file_get_contents($url);
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

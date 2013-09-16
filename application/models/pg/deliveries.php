<?php
class PG_Deliveries extends SimpleData
{
	public static $table = 'pg_programme_deliveries';
	public static $rules = array();

	// On save, also generate deliveries cache
	public function save(){
		$success = parent::save();
		$this->generate_cache();
		return $success;
	}

	public function delete(){
		$p = PG_Programme::where('id','=',$this->programme_id)->first(array('id','instance_id','year'));
		// Clear caches
		Cache::forget("api-output-pg/programme-{$p->year}-{$p->instance_id}");
		// Remove value (actually remove via raw delete, not just hide)
		$deleted = static::raw_delete();

		$this->generate_cache();

		return $deleted;
	}

	public function award(){
		return $this->belongs_to('PG_Award', 'award');
	}


	// Generate deliveries cache
	protected function generate_cache(){
		// Find instance id & year
		$p = PG_Programme::where('id','=',$this->programme_id)->first(array('id','instance_id','year'));
		// Generate cache
		static::generate_programme_deliveries($p->instance_id, $p->year, $p->id);

		//forget output cache
		Cache::forget("api-output-pg/programme-{$p->year}-{$p->instance_id}");
	}

	// get data from cache (or make a new one)
	public static function get_programme_deliveries($iid, $year){
		$key = "pg-deliveries.{$iid}-{$year}";
		return (Cache::has($key)) ? Cache::get($key) : static::generate_programme_deliveries($iid, $year); 
	}

	// generate new cache data
	public static function generate_programme_deliveries($iid, $year, $pid = false){
		$key = "pg-deliveries.{$iid}-{$year}";

		// Find programme id if none provided
		if(!$pid){
			$p = PG_Programme::where('instance_id','=',$iid)->where('year','=',$year)->first(array('id'));
			if($p === null) return array();
			$pid = $p->id;
		}	
		
		$cacheable = array();
		// Get all deliveries for this programme
		$data = static::where('programme_id','=',$pid)->get();
		// format data
		foreach($data as $delivery){ $cacheable[] =  $delivery->attributes; }
		// cache it
		Cache::put($key, $cacheable,100000);

		return $cacheable;
	}


	/**
	 * generate API data (overides copy in simpledata to avoid use of "Hidden")
	 * Get live version of API data from database
	 *
	 * @param year (Unused - PHP requires signature not to change)
	 * @param data (Unused - PHP requires signature not to change)
	 */
	public static function generate_api_data($year = false, $data = false)
	{
		// keys
		$model = strtolower(get_called_class());
		$cache_key = 'api-'.$model;
		// make data
		$data = array();
		foreach (static::get() as $record) {
			// Direct grab of attributes is faster than to_array 
			// since don't need to worry about realtions & things like that
			$data[$record->attributes["id"]] = $record->attributes;
		}
		// Store data in to cache
		Cache::put($cache_key, $data, 2628000);
		// return
		return $data;
	}

}
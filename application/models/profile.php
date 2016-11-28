<?php
abstract class Profile extends SimpleData
{

	public static $type = null;

	public static $rules = array();

	// Use all these fields when producing a "list box"
	public static $list_fields = array('id', 'name', 'course');


	// Pretty print name when requested
	public function get_name()
	{
		return $this->attributes['course'].' - '.$this->attributes['name'];
	}

	public static function generate_api_data($year = false, $data = false)
	{
		// keys
		$model = strtolower(get_called_class());
		$cache_key = 'api-'.$model;
		// make data
		$data = array();
		foreach (static::where('hidden', '=', 0)->get() as $record) {
			// Direct grab of attributes is faster than to_array
			// since don't need to worry about realtions & things like that
			$data[$record->attributes["id"]] = $record->attributes;
			$cat_class = strtoupper(static::$type) . '_SubjectCategory';
			$data[$record->attributes["id"]]['subject_categories'] = $cat_class::replace_ids_with_values($data[$record->attributes["id"]]['subject_categories'], false, true);
		}
		// Store data in to cache
		Cache::put($cache_key, $data, 2628000);
		// return
		return $data;
	}

	public function toArray(){
		$data = $this->attributes;

		$cat_class = strtoupper(static::$type) . '_SubjectCategory';
		$data['subject_categories'] = $cat_class::replace_ids_with_values($data['subject_categories'], false, true);
		return $data;
	}

}
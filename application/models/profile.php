<?php
abstract class Profile extends SimpleData
{

	public static $type = null;

	public static $rules = array(
		'slug' => 'required'
	);

	// Use all these fields when producing a "list box"
	public static $list_fields = array('id', 'name', 'course');


	// Pretty print name when requested
	public function get_name()
	{
		return $this->attributes['course'].' - '.$this->attributes['name'] . ' - (' . ucfirst($this->attributes['type']) . ')';
	}

	public static function generate_api_data($year = false, $data = false)
	{
		// keys
		$model = strtolower(get_called_class());
		$cache_key = 'api-'.$model;
		// make data
		$data = array();
		foreach (static::where('hidden', '=', 0)->where('type', '!=', 'alumni')->get() as $record) {
			// Direct grab of attributes is faster than to_array
			// since don't need to worry about realtions & things like that
			$data[$record->attributes["id"]] = $record->attributes;
			$cat_class = strtoupper(static::$type) . '_SubjectCategory';
			$data[$record->attributes["id"]]['subject_categories'] = $cat_class::replace_ids_with_values($data[$record->attributes["id"]]['subject_categories'], false, true);
			$data[$record->attributes["id"]]['banner_image_id'] = Image::replace_ids_with_values($data[$record->attributes["id"]]['banner_image_id'], false, false, true);
			$data[$record->attributes["id"]]['profile_image_id'] = Image::replace_ids_with_values($data[$record->attributes["id"]]['profile_image_id'], false, false, true);
		}
		// Store data in to cache
		Cache::put($cache_key, $data, 2628000);
		// return
		return $data;	}

	public function toArray(){
		$data = $this->attributes;

		$cat_class = strtoupper(static::$type) . '_SubjectCategory';
		$data['subject_categories'] = $cat_class::replace_ids_with_values($data['subject_categories'], false, true);
		$data['banner_image_id'] = Image::replace_ids_with_values($data['banner_image_id'], false, false, true);
		$data['profile_image_id'] = Image::replace_ids_with_values($data['profile_image_id'], false, false, true);
		return $data;
	}

	/**
	 * Override Eloquent's save so that we generate a new json file for our API
	 */
	public function save()
	{
		try {
			Cache::purge('profile-'. static::$type);
		} catch (\Filch\FilchException $e ){
			// we don't mind
		}
	
		return parent::save();
	}
}
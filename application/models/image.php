<?php
class Image extends SimpleData
{
	public static $table = 'images';

	public static $rules = array(
		'name'  => 'required',
		'image' => 'mimes:jpg|max:1000'
	);

	public function populate_from_input()
	{
		if (is_null(static::$validation))
		{
			throw new NoValidationException('No validation');
		}

		$input = Input::all();

		// Remove _wysihtml5_mode entirely.
		unset($input['image']);
		unset($input['_wysihtml5_mode']);

		$this->fill($input);
	}

	public function url(){
		return URL::base().'/media/'.$this->id.'.jpg';
	}

	public function thumb_url(){
		return URL::base().'/media/'.$this->id.'_thumb.jpg';
	}

	public function path(){
		return  path('storage').'images/'.$new->id.'.jpg';
	}


	/**
	 * generate API data
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
		foreach (static::where('hidden', '=', 0)->get() as $record) {
			// Direct grab of attributes is faster than to_array 
			// since don't need to worry about realtions & things like that
			$data[$record->attributes["id"]] = $record->attributes;
			$data[$record->attributes["id"]]['url'] = URL::base().'/media/'.$record->attributes['id'].'.jpg';
			$data[$record->attributes["id"]]['thumb'] = URL::base().'/media/'.$record->attributes['id'].'_thumb.jpg';
		}
		// Store data in to cache
		Cache::put($cache_key, $data, 2628000);
		// return
		return $data;
	}
	/**
	 * to array, add extra vals
	 */
	public function to_array(){
		$data = $this->attributes;

		$data['url'] = $this->url();
		$data['thumb'] = $this->thumb_url();

		return $data;
	}
}
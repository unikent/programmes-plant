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

	/**
	 * Web path to image
	 */
	public function url(){
		return URL::base().'/media/'.$this->id.'.jpg';
	}

	/**
	 * Web path to thumb
	 */
	public function thumb_url(){
		return URL::base().'/media/'.$this->id.'_thumb.jpg';
	}

	/**
	 * File path to image
	 */
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
			$data[$record->attributes["id"]] = $record->to_array();
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

		$data['alt_text'] = $data['alt'];
		$data['mime_type'] = "image/jpeg";

		$data['attribution'] = array(
			'link' => $data['attribution_link'],
			'author' => $data['attribution_text'],
			'license' => $data['licence_link']
		);

		//die($data['width'], $data['height']);
		list($thumb_height, $thumb_width) = static::getThumbSize(170, $data['width'], $data['height']);

		$data['sizes'] = array(
			'full' => array('url' => $this->url(), 'width'=> (int) $data['width'], 'height'=> (int)$data['height']),
			'thumbnail' => array('url' => $this->thumb_url(), 'width'=> $thumb_width, 'height'=> $thumb_height),
		);

		foreach(array('alt','width','height','attribution_link','attribution_text','licence_link','hidden') as $k){
			unset($data[$k]);
		}

		return $data;
	}

	/**
	 * get thumb Size from full image dimensions
	 */
	public static function getThumbSize($desired_width, $original_width, $original_height){
		// check valid values are provided
		if(empty($original_height) || empty($original_width)) return array(null,null);

		if($original_width > $original_height){
			$desired_height = floor($original_height * ($desired_width / $original_width));
		}else{
			$desired_height = $desired_width;
			$desired_width = floor($original_width * ($desired_height / $original_height));
		}	

		return array($desired_height, $desired_width);
	}
}
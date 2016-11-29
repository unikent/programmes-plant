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
	
	public function path(){
		return  path('storage').'images/'.$new->id.'.jpg';
	}
}
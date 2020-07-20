<?php
abstract class ProgrammeField extends Field
{
	public static $table = '';

	/**
	 * Stores programme field types.
	 */
	public static $types = array(
									'NORMAL' => 0,
									'DEFAULT' => 1,
									'OVERRIDABLE_DEFAULT' => 2
								);

	/**
	 * a cache of this model
	 */
	public static $api_cache = array();

	public static function get_types_as_list()
	{
		$list_types = array();
		foreach (self::$types as $key => $value) {
			$list_types[$value] = $key;
		}
		return $list_types;
	}
	
	/**
	 * Gets programme sections and fields in an array
	 *
	 * gets all programme sections and the fields within each, and orders the sections by their order value
	 * a double-loop then goes through each section and builds up an array of ordered fields
	 *
	 * @return array $sections_array
	 */
	public static function programme_fields_by_section()
	{
		$fieldModel = static::$type.'_programmefields';
		$sectionModel = static::$type.'_ProgrammeSection';

		// get the section and field data
		$sections = $sectionModel::with("programmefields")->order_by('order','asc')->get();

		$sections_array = array();

		foreach ($sections as $section)
		{
			foreach ($section->$fieldModel as $programmefield)
			{
				// make sure the section is active
				// and user has permission to read the field
				
				if ($section->id > 0 && Auth::user()->can(URLParams::get_type()."_fields_read_{$programmefield->colname}"))
				{
					// build up the final array indexed by section name and programme field order
					$sections_array[$section->name][$programmefield->order] = $programmefield;
				}
			}
			// sort each section sub-array so that the fields are in the correct order
			if (isset($sections_array[$section->name])) ksort($sections_array[$section->name]);
		}
		return $sections_array;
	}
	
	public static function programme_fields()
	{
		$fieldModel = URLParams::get_type()."_ProgrammeField";
		return static::where('active','=','1')->where_in('programme_field_type', array(ProgrammeField::$types['OVERRIDABLE_DEFAULT'], $fieldModel::$types['NORMAL']))->order_by('order','asc')->get();
	}
	
	/**
	* assign_fields()
	*
	* loop through the programme fields, assigning the user input value to the appropriate column name
	*
	* @param object $programme_obj the programme object
	* @param array $programme_fields programme fields from db
	* @param array $input_fields user input fields from the form
	* @return object $programme_obj modified programme object
	*/
	public static function assign_fields($programme_obj, $programme_fields, $input_fields, $user=null)
	{
		if ($user == null)
		{
		   $user = Auth::user();
		}

		foreach ($programme_fields as $programme_field)
		{
			$colname = $programme_field->colname;

			// make sure the field is being used (if it's in section 0 then it isn't so ignore it completely)
			if ($programme_field->section > 0)
			{
				// if the field is a file type...
				if('file' === $programme_field->field_type ) {
					// has the user asked to forget the current field?
					if(isset($input_fields[$colname . '_clear'])) {
						$input_fields[$colname] = '';
					}
					
					// process the upload if a file has been uploaded
					$uploadDetails = Input::file($colname . '_upload');
					if(null != $uploadDetails) {
						if ($uploadDetails['error'] === UPLOAD_ERR_OK) {
							$input_fields[$colname] = self::process_file_input_field($programme_obj, $programme_field, Input::file($colname . '_upload'));
						}
					}
				}
				// if the field is being used add its value to the appropriate colname in the programme object
				//if (isset($input_fields[$colname]) && $user->can(\URLParams::get_type()."_fields_write_{$colname}")) {
				if (isset($input_fields[$colname]) && $user->can(\URLParams::get_type()."_fields_write_{$colname}")) {
					// if the field's value is an array, convert it into a comma-separated string
					if (is_array($input_fields[$colname]))
					{
						$input_fields[$colname] = implode(',', $input_fields[$colname]);
					}
					$programme_obj->$colname = $input_fields[$colname];
				}
			}
		}
		return $programme_obj;
	}

	public static function process_file_input_field($programme, $programme_field, $upload)
	{
		if(is_uploaded_file($upload['tmp_name'])) {
			$id = $programme->id ? $programme->id : 'new';
			$relative_path = 'programmes/' . $id . '/' . static::niceifyFilename($programme_field->field_name);
			$upload_path = Config::get('images.upload_directory', path('storage').'/uploads') . '/' . $relative_path;
			$filename = static::niceifyFilename(date('YmdHis') . '_' . $upload['name']);
			if(!file_exists($upload_path)) {
				mkdir($upload_path, Config::get('images.upload_dir_mode', 0754), true);
			}
			if( move_uploaded_file($upload['tmp_name'], $upload_path . '/' . $filename)) {
				return Config::get('images.upload_public_url') . '/' . $relative_path . '/' . $filename;
			}
		}
		return '';
	}

	/**
	 * Replace any non alphanumeric or ._- characters with _, ensure there are no more than one _- or . in a row
	 * @param $input - Input filename
	 * @return string
	 */
	public static function niceifyFilename($input)
	{
		return
			preg_replace('/[_-]{2,}/', '_',
				preg_replace('/\.{2,}/', '.',
					preg_replace('/[^a-z0-9_.-]/i', '_', $input)
				)
			);
	}

	/**
	 * Extract input into model.
	 */
	public function get_input()
	{
		parent::get_input();
		$this->programme_field_type =  Input::get('programme_field_type');
	}

	/**
	 * Override Eloquent's save so that we generate a new json file for our API 
	 */
	public function save()
	{
		//clear model memory cache
		static::$api_cache = array();

		$saved = parent::save();
		
		if($saved){
			static::generate_api_data();
		}
		
		return $saved;
	}

	/**
	 * get API Data
	 * Return cached data from data type
	 *
	 * @param year (Unused - PHP requires signature not to change)
	 * @return data Object
	 */
	public static function get_api_data(){
		// generate keys
		$model = strtolower(get_called_class());
		$cache_key = 'api-'.$model;

		if(isset(static::$api_cache[$cache_key])) return static::$api_cache[$cache_key];

		// Get data from cache (or generate it)
		return static::$api_cache[$cache_key] = (Cache::has($cache_key)) ? Cache::get($cache_key) : static::generate_api_data();
	}

	/**
	 * generate API data
	 * Get live version of API data from database
	 *
	 */
	public static function generate_api_data(){
		// keys
		$model = strtolower(get_called_class());
		$cache_key = 'api-'.$model;

		$data = array();
		
		$fields = static::where_in('field_type', array('table_select', 'table_multiselect','image'))->get(array('colname', 'field_type', 'field_meta'));
		foreach ($fields as $record) {   
				$data[$record->attributes["colname"]] =  $record->attributes["field_type"]=='image'?'Image': $record->attributes["field_meta"];
		}
		// Store data in to cache
		Cache::put($cache_key, $data, 2628000);
		// return
		return $data;
	}
}

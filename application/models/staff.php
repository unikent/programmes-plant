<?php
class Staff extends SimpleData
{
	public static $table = 'research_staff';

	public static $rules = array();

	// Use all these fields when producing a "list box"
	public static $list_fields = array('id', 'login', 'forename', 'surname', 'title','subject');

	private $subjects_cache = array();

	// Load user data from ldap, using users login name
	public function load_from_ldap(){
		// Attempt to load user from ldap
		$ldap = LDAPConnect::instance();
		$attributes = $ldap->getUserAnonymous($this->attributes['login']);
		// If attributes were returned, use them
		if($attributes != false){
			// title?
			$this->email = isset($attributes[0]['mail'][0]) ? $attributes[0]['mail'][0] : ''; 
			$this->forename = isset($attributes[0]['givenname'][0]) ? $attributes[0]['givenname'][0] : '';
			$this->surname = isset($attributes[0]['sn'][0]) ? $attributes[0]['sn'][0] : ''; 
		}
	}

	// Load LDAP attributes on save
	public function save(){
		// populate ldap attributes
		$this->load_from_ldap();

		parent::save();
	}

	// use "Login" instead of name as title
	public static function get_title_field()
	{
		return 'login';
	}


	public function get_login()
	{
		if(sizeof($this->subjects_cache)===0)$this->subjects_cache = PG_Subject::all_as_list();

		return $this->attributes['forename'].' '.$this->attributes['surname'].' ('.$this->attributes['login'].') - '.$this->subjects_cache[$this->attributes['subject']];
	}


	// Pretty print name when requested
	public function get_name()
	{
		if(sizeof($this->subjects_cache)===0)$this->subjects_cache = PG_Subject::all_as_list();
		return $this->attributes['title'].' '.$this->attributes['forename'].' '.$this->attributes['surname'].' ('.$this->attributes['login'].') - '.$this->subjects_cache[$this->attributes['subject']];
	}

	// custom replace with ids function, since "name" doesn't exist for this type
	public static function replace_ids_with_values($ids, $year = false, $titles_only = false)
	{
		// If nothing is set, return an empty array
		if(trim($ids) == '') return array();
		// Get list of ids to swap out & grab api data from cache
		$id_array = explode(',', $ids);
		$cached_data = static::get_api_data();
		// Create new array of actual values matching the ids from the cache
		$values = array();
		foreach ($id_array as $id) 
		{
			if($titles_only)
			{
				$values[] = isset($cached_data[$id]) ? $cached_data[$id]['forename'].' '.$cached_data[$id]['surname'] : '';
			}
			else
			{
				$values[] = isset($cached_data[$id]) ? $cached_data[$id] : '';
			}
		}

		return $values;
	}
}
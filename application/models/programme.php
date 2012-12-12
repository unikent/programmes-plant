<?php
class Programme extends Revisionable
{
	public static $table = 'programmes';
    public static $timestamps = true;
    public $revision = false;
    protected $revision_model = 'ProgrammeRevision';
    protected $revision_type = 'programme';
    protected $revision_table = 'programmes_revisions';
    
    /**
     * Get the name of the title field/column in the database.
     * 
     * @return The name of the title field.
     */
    public static function get_title_field()
    {
    	return 'programme_title_1';
    }

    /**
     * Get the name of the slug field/column in the database.
     * 
     * @return The name of the slug field.
     */
    public static function get_slug_field()
    {
        return 'slug_2';
    }

    /**
     * Get the name of the subject area 1 field/column in the database.
     * 
     * @return The name of the subject area 1 field.
     */
    public static function get_subject_area_1_field()
    {
        return 'subject_area_1_8';
    }

    /**
     * Get the name of the award field/column in the database.
     * 
     * @return The name of the award field.
     */
    public static function get_award_field()
    {
        return 'award_3';
    }
    
    /**
     * Get the name of the 'programme withdrawn' field/column in the database.
     * 
     * @return The name of 'programme withdrawn the  field.
     */
    public static function get_withdrawn_field()
    {
        return 'programme_withdrawn_54';
    }
    
    /**
     * Get the name of the 'programme suspended' field/column in the database.
     * 
     * @return The name of the 'programme suspended' field.
     */
    public static function get_suspended_field()
    {
        return 'programme_suspended_53';
    }
    
    /**
     * Get the name of the 'subject to approval' field/column in the database.
     * 
     * @return The name of the 'subject to approval' field.
     */
    public static function get_subject_to_approval_field()
    {
        return 'subject_to_approval_52';
    }
    
    /**
     * Get this proramme's award.
     * 
     * @return Award The award for this programme.
     */
    public function award()
    {
      return $this->belongs_to('Award', static::get_award_field());
    }

    
    /**
     * look through the passed in record and substitute any ids with data from their correct table
     * primarily for our json api
     * 
     * @param $record The record
     * @return $new_record A new record with ids substituted
     */
    public static function pull_external_data($record)
    {
        $path = path('storage') . 'api/';
        $programme_fields_path = $path . 'ProgrammeField.json';

        //if we dont have a json file, return the $record as it was
        if (!file_exists($programme_fields_path))
        {
            return $record;
        }

        //get programme fields
        $programme_fields = json_decode(file_get_contents($programme_fields_path));
        
        //make neater programme fields array
        $fields_array = array();
        foreach ($programme_fields as $field) {
            $fields_array[$field->colname] = $field->field_meta;
        }
        
        //substitute the concerned ids with actual data
        $new_record = array();
        foreach ($record as $field_name => $field_value) {
            if(isset($fields_array[$field_name])){
                $model = $fields_array[$field_name];
                $field_value = $model::replace_ids_with_values($field_value);
            }
            $new_record[$field_name] = $field_value;
        }

        return $new_record;
    }

    /**
     * This function replaces the passed-in ids with their actual record
     * Limiting the record to its name and id
     */
    public static function replace_ids_with_values($ids){

        $ds_fields = static::where_in('id', explode(',', $ids))->get();
        $values = array();
        foreach ($ds_fields as $ds_field) {
            $title_field = static::get_title_field();
            $slug_field = static::get_slug_field();
            $values[$ds_field->id] = static::remove_ids_from_field_names(array(
                    'id' => $ds_field->id,
                    $title_field => $ds_field->$title_field,
                    $slug_field => $ds_field->$slug_field
                ));
        }

        return $values;
    }
    
}

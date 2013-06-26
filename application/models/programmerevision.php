<?php
abstract class ProgrammeRevision extends Revision
{
    public static $table = 'programmes_revisions';
    protected $data_type_id = 'programme_id';


    /**
     * Get this programme's administrative school.
     * 
     * @return School The administrative school for this programme.
     */
    public function administrative_school()
    {
        $model = static::$programme_model;
       return $this->belongs_to('School', $model::get_administrative_school_field());
    }

    /**
     * Get this programme's additional school.
     * 
     * @return School The additional school for this programme.
     */
    public function additional_school()
    {
        $model = static::$programme_model;
        return $this->belongs_to('School', $model::get_additional_school_field());
    }

    /**
     * Get this programme's campus.
     * 
     * @return School The additional school for this programme.
     */
    public function location()
    {
        $model = static::$programme_model;
        return $this->belongs_to('Campus', $model::get_location_field());
    }

    

   
}

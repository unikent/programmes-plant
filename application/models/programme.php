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
        return 'programme_withdrawn_64';
    }
    
    /**
     * Get the name of the 'programme suspended' field/column in the database.
     * 
     * @return The name of the 'programme suspended' field.
     */
    public static function get_suspended_field()
    {
        return 'programme_suspended_63';
    }
    
    /**
     * Get the name of the 'subject to approval' field/column in the database.
     * 
     * @return The name of the 'subject to approval' field.
     */
    public static function get_subject_to_approval_field()
    {
        return 'subject_to_approval_62';
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
    
}

<?php
class Programme extends Revisionable
{
	public static $table = 'programmes';
    public static $timestamps = true;
    public $revision = false;
    protected $revision_model = 'ProgrammeRevision';
    protected $revision_type = 'programme';
    protected $revision_table = 'programmes_revisions';
    
    public static function get_title_field()
    {
    	return 'programme_title_1';
    }
    
    public static function get_award_field()
    {
        return 'award_3';
    }
    
    public static function get_withdrawn_field()
    {
        return 'programme_withdrawn_64';
    }
    
    public static function get_suspended_field()
    {
        return 'programme_suspended_63';
    }
    
    public static function get_subject_to_approval_field()
    {
        return 'subject_to_approval_62';
    }
    
    public function award()
    {
      return $this->belongs_to('Award', 'award_3');
    }
    
}

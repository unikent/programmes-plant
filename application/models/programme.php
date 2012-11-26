<?php
class Programme extends Revisionable
{
    public static $timestamps = true;
    public $revision = false;
    protected $revision_model = 'ProgrammeRevision';
    protected $revision_type = 'programme';
    protected $revision_table = 'programmes_revisions';
    
    public static function get_title_field(){
    	return 'programme_title_1';
    }
    
}

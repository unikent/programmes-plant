<?php
class ProgrammeRevision extends Eloquent
{
    public static $table = 'programmes_revisions';

    /**
     * Get this programme's award.
     * 
     * @return Award The award for this programme.
     */
    public function award()
    {
      return $this->belongs_to('Award', Programme::get_award_field());
    }

    /**
     * Get this programme's first subject area.
     * 
     * @return Subject The first subject area for this programme.
     */
    public function subject_area_1()
    {
      return $this->belongs_to('Subject', Programme::get_subject_area_1_field());
    }

    /**
     * Get this programme's administrative school.
     * 
     * @return School The administrative school for this programme.
     */
    public function administrative_school()
    {
      return $this->belongs_to('School', Programme::get_administrative_school_field());
    }

    /**
     * Get this programme's additional school.
     * 
     * @return School The additional school for this programme.
     */
    public function additional_school()
    {
      return $this->belongs_to('School', Programme::get_additional_school_field());
    }

    /**
     * Get this programme's campus.
     * 
     * @return School The additional school for this programme.
     */
    public function location()
    {
      return $this->belongs_to('Campus', Programme::get_location_field());
    }

    public function get_identifier(){
        return "R{$this->programme_id}-{$this->id}";
    }

    public function get_identifier_string(){
        return '<strong>'.$this->get_identifier().'</strong> created '.$this->get_created_time().' by '.$this->edits_by ;
    }

    public function get_published_time(){
        return Date("jS F Y \a\\t H:i:s" ,strtotime($this->published_at));
    }
    public function get_created_time(){
        return Date("jS F Y \a\\t H:i:s", strtotime($this->created_at));
    }

   
}

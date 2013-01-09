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

}

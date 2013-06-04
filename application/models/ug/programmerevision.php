<?php
class UG_ProgrammeRevision extends ProgrammeRevision
{
    public static $table = 'programmes_revisions_ug';
    protected $data_type_id = 'programme_id';

    /**
     * Get this programme's award.
     * 
     * @return Award The award for this programme.
     */
    public function award()
    {
      return $this->belongs_to('Award', UG_Programme::get_award_field());
    }

    /**
     * Get this programme's first subject area.
     * 
     * @return Subject The first subject area for this programme.
     */
    public function subject_area_1()
    {
      return $this->belongs_to('Subject', UG_Programme::get_subject_area_1_field());
    }

    /**
     * Get this programme's administrative school.
     * 
     * @return School The administrative school for this programme.
     */
    public function administrative_school()
    {
      return $this->belongs_to('School', UG_Programme::get_administrative_school_field());
    }

    /**
     * Get this programme's additional school.
     * 
     * @return School The additional school for this programme.
     */
    public function additional_school()
    {
      return $this->belongs_to('School', UG_Programme::get_additional_school_field());
    }

    /**
     * Get this programme's campus.
     * 
     * @return School The additional school for this programme.
     */
    public function location()
    {
      return $this->belongs_to('Campus', UG_Programme::get_location_field());
    }
}

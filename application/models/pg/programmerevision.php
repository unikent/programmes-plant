<?php
class PG_ProgrammeRevision extends ProgrammeRevision
{
    public static $table = 'programmes_revisions_pg';
    protected $data_type_id = 'programme_id';
    public static $programme_model = 'PG_Programme';

     /**
     * Get this programme's award.
     * 
     * @return Award The award for this programme.
     */
    public function award()
    {
      return $this->belongs_to('PG_Award', PG_Programme::get_award_field());
    }

    /**
     * Get this programme's first subject area.
     * 
     * @return Subject The first subject area for this programme.
     */
    public function subject_area_1()
    {
      return $this->belongs_to('PG_Subject', PG_Programme::get_subject_area_1_field());
    }
}

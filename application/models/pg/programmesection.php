<?php

class PG_ProgrammeSection extends ProgrammeSection {
    public static $table = 'programmesections_pg';

        public function programmefields()
    {
        return $this->pg_programmefields();
    }
}
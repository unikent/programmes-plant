<?php

class UG_ProgrammeSection extends ProgrammeSection {
    public static $table = 'programmesections_ug';

    public function programmefields()
    {
        return $this->ug_programmefields();
    }
}
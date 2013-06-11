<?php
class PG_ProgrammeRevision extends ProgrammeRevision
{
    public static $table = 'programmes_revisions_pg';
    protected $data_type_id = 'programme_id';
    public static $programme_model = 'PG_Programme';
}

<?php
class PG_ProgrammeField extends ProgrammeField
{
    public static $table = 'programmes_fields_pg';
    public static $type = 'pg';
    public static $sections_model = 'PG_ProgrammeSection';
}

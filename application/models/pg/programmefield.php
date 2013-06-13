<?php
class PG_ProgrammeField extends ProgrammeField
{
    public static $table = 'programmes_fields_pg';
    public static $type = 'pg';
    public static $sections_model = 'PG_ProgrammeSection';

    // Schemas to update when fields are added
    protected static $schemas = array(
    	'PG_Programme',
		'PG_ProgrammeRevision',
		'PG_ProgrammeSetting',
		'PG_ProgrammeSettingRevision'
	);
}

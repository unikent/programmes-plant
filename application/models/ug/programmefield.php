<?php
class UG_ProgrammeField extends ProgrammeField
{
    public static $table = 'programmes_fields_ug';
    public static $type = 'ug';
    public static $sections_model = 'UG_ProgrammeSection';

    // Schemas to update when fields are added
    protected static $schemas = array(
    	'UG_Programme',
		'UG_ProgrammeRevision',
		'UG_ProgrammeSetting',
		'UG_ProgrammeSettingRevision'
	);
}

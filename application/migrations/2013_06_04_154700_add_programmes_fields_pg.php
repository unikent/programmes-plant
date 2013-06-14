<?php
Config::set('verify::verify.prefix', 'usersys');
class Add_Programmes_Fields_Pg {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		$this->addProgrammeFields();
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		$this->removeProgrammeFields();
	}



	public function addProgrammeFields(){
		$programme_fields = array();
		$programme_override_fields = array();
		$programme_setting_fields = array();

		// standard fields which are required by the system for it to work properly
		// array format: title, type, hints, options, section (except setting_fields)
		$programme_fields[] = array('Programme title', 'text', 'eg Accounting and Finance and Economics', '', 1);
		$programme_fields[] = array('Slug', 'text', '', '', 1);
		$programme_fields[] = array('Award', 'table_select', '', 'Award', 2);
		$programme_fields[] = array('Subject area 1', 'table_select', 'Please select the main subject area for this programme.  If the programme is joint honours, please ensure the subject selected is taught by the administrative school.', 'Subject', 2);
		$programme_fields[] = array('Programme withdrawn', 'text', 'Only tick the box if the programme has been completely withdrawn. Please unpublish the programme if necessary or add a holding message.', '', 10);
		$programme_fields[] = array('Programme suspended', 'text', 'Only tick the box if the programme has been suspended. Please unpublish the programme if necessary or add a holding message.', '', 10);
		$programme_fields[] = array('Subject to approval', 'text', 'Check the box if this programme has been approved for advertising subject to approval by PASC.', '', 10);
		
		// Add the fields in
		foreach ($programme_fields as $field) {
			$this->add_field('PG_ProgrammeField', array('programme_settings_pg', 'programmes_pg', 'programme_settings_revisions_pg', 'programmes_revisions_pg'), $field[0], $field[1], $field[2], $field[3], PG_ProgrammeField::$types['NORMAL'], $field[4]);
		}

	}


	/**
	 * Adds a field to a fields table.
	 * 
	 * @param string $modelname the class of object we are creating. eg. 'ProgrammeSettingField' or 'Programme_Field'
	 * @param string $tablename the table name we're creating a field for 
	 * @param string $title the title of the field.
	 * @param string $type the type of field.
	 * @param string $hints the hints for the field.
	 * @param string $options the options, particularly used when the field type is select.
	 */
	public function add_field($modelname, $tablename, $title, $type, $hints, $options, $programme_field_type = 0, $section = 0)
	{
        // define the column name
    	$colname = Str::slug($title, '_');
    	
    	// set up the field object and save it to the _fields table
    	// eg for a Programme_Setting object we set up the ProgrammeSettingField object and save it to the programme_settings_fields table
    	$field_object = new $modelname;
        $field_object->field_name = $title;
        $field_object->field_type = $type;
        $field_object->field_description = $hints;
        $field_object->field_meta = $options;
        $field_object->field_initval =  '';
        $field_object->active = 1;
        $field_object->view = 1;
        $field_object->section = $section;
        $field_object->programme_field_type = $programme_field_type;
        $field_object->raw_save();
    	$colname .= '_'.$field_object->id;
    	$field_object->colname = $colname;

    	$field_object->raw_save();

		
		if(!is_array($tablename)){
			$tablename = array($tablename);
		}

		foreach ($tablename as $value) {
			// modify the schema for the main table eg programme_settings
			// by default columns are varchars unless they've been specified as textareas
			Schema::table($value, function($table) use ($colname, $type) {
			
				if ($type=='textarea')
				{
	    			$table->text($colname);
				}
				else
				{
					$table->string($colname, 255);
				}
					
			});
		}

		// set up read/write permissions for the field
		$permission = new Permission;
		$permission->name = "pg_fields_read_{$field_object->colname}";
		$permission->save();
		$permission->roles()->sync(array(2, 3)); // Grant read rights to Admin and User as default

		$permission = new Permission;
		$permission->name = "pg_fields_write_{$field_object->colname}";
		$permission->save();
		$permission->roles()->sync(array(2, 3)); // Grant read rights to Admin and User as default
	}



	public function removeProgrammeFields(){
		$programme_fields = array();
		$programme_override_fields = array();
		$programme_setting_fields = array();

		// standard fields which are required by the system for it to work properly
		// array format: title, type, hints, options, section (except setting_fields)
		$programme_fields[] = array('Programme title', 'text', 'eg Accounting and Finance and Economics', '', 1);
		$programme_fields[] = array('Award', 'table_select', '', 'Award', 2);
		$programme_fields[] = array('Subject area 1', 'table_select', 'Please select the main subject area for this programme.  If the programme is joint honours, please ensure the subject selected is taught by the administrative school.', 'Subject', 2);
		$programme_fields[] = array('Programme withdrawn', 'text', 'Only tick the box if the programme has been completely withdrawn. Please unpublish the programme if necessary or add a holding message.', '', 10);
		$programme_fields[] = array('Programme suspended', 'text', 'Only tick the box if the programme has been suspended. Please unpublish the programme if necessary or add a holding message.', '', 10);
		$programme_fields[] = array('Subject to approval', 'text', 'Check the box if this programme has been approved for advertising subject to approval by PASC.', '', 10);
		
		// remove the fields
		foreach ($programme_fields as $field) {
			$this->remove_field('PG_ProgrammeField', array('programme_settings_pg', 'programmes_pg','programme_settings_revisions_pg', 'programmes_revisions_pg'), $field[0]);
		}
	}

	/**
	 * Removes a field to a fields table.
	 * 
	 * @param string $modelname the class of object we are creating. eg. 'ProgrammeSettingField' or 'Programme_Field'
	 * @param string $tablename the table name we're creating a field for 
	 * @param string $title the title of the field.
	 * @param string $type the type of field.
	 * @param string $hints the hints for the field.
	 * @param string $options the options, particularly used when the field type is select.
	 */
	public function remove_field($modelname, $tablename, $title)
	{
        // define the column name
    	$colname = Str::slug($title, '_');
    	
    	// delete all the fields
    	$field_object = new $modelname;
    	
    	foreach($modelname::all() as $obj )$obj->delete();
 

		// delete all the fields from the programmes and programme settings tables too
		if(!is_array($tablename)){
			$tablename = array($tablename);
		}
		foreach ($tablename as $value) {
			// modify the schema for the main table eg programme_settings
			// by default columns are varchars unless they've been specified as textareas
			Schema::table($value, function($table) use ($colname) {
	    		$table->drop_column($colname);
			});
		}

		// revoke permissions
		$permissions = array_merge(
			Permission::where('name', '=', "fields_read_{$field_object->colname}")->get(),
			Permission::where('name', '=', "fields_write_{$field_object->colname}")->get()
		);
		foreach($permissions as $permission){
			$permission->roles()->sync(array());
			$permission->delete();
		}

	}

}
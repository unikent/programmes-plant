<?php

class Add_More_Programme_Fields {

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
		//
	}

	public function addProgrammeFields(){
		$programme_fields = array();
		$programme_override_fields = array();
		$programme_setting_fields = array();
		
		//Programme fields
		$programme_fields[] = array('Did you know', 'textarea', '', '');
		$programme_fields[] = array('Quote', 'textarea', '', '');
		
		//Overrides
		$programme_override_fields[] = array('Entry profile', 'textarea', '', '');
		$programme_override_fields[] = array('Home/EU intro text', 'textarea', '', '');
		$programme_override_fields[] = array('International student intro text', 'textarea', '', '');
		$programme_override_fields[] = array('English language requirements', 'textarea', '', '');

		// Add the fields in
		foreach ($programme_fields as $field) {
			$this->add_field('ProgrammeField', array('programme_settings', 'programmes'), $field[0], $field[1], $field[2], $field[3], ProgrammeField::$types['NORMAL']);
		}

		// Add the fields in
		foreach ($programme_setting_fields as $field) {
			$this->add_field('ProgrammeField', array('programme_settings', 'programmes'), $field[0], $field[1], $field[2], $field[3], ProgrammeField::$types['DEFAULT']);
		}

		// Add the fields in
		foreach ($programme_override_fields as $field) {
			$this->add_field('ProgrammeField', array('programme_settings', 'programmes'), $field[0], $field[1], $field[2], $field[3], ProgrammeField::$types['OVERRIDABLE_DEFAULT']);
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
	public function add_field($modelname, $tablename, $title, $type, $hints, $options, $programme_field_type = 0)
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
        $field_object->programme_field_type = $programme_field_type;
        $field_object->save();
    	$colname .= '_'.$field_object->id;
    	$field_object->colname = $colname;
    	$field_object->save();
		
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
			
			// modify the schema for the revisions table eg programme_settings_revisions
			// by default columns are varchars unless they've been specified as textareas
			Schema::table($value.'_revisions', function($table) use ($colname, $type) {
			
				if ($type=='textarea')
				{
					$table->text($colname);
				}
				else
				{
					$table->string($colname,255);
				}
				
			});
		}
	}

}
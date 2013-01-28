<?php

class Add_Setting_Fields_To_Globals {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		$this->add_field('GlobalSettingField', 'global_settings', 'Careers/employability text', 'textarea', 'This text will appear under the text on the Careers tab', '');
		$this->add_field('GlobalSettingField', 'global_settings', 'Foundation year', 'textarea', 'This text will appear under the foundation year text on the Modules tab.', '');
		$this->add_field('GlobalSettingField', 'global_settings', 'General disclaimer', 'textarea', 'This text will appear at the footer of each page', '');
		$this->add_field('GlobalSettingField', 'global_settings', 'Module disclaimer', 'textarea', 'This text will appear under each stage on the Modules tab', '');
		$this->add_field('GlobalSettingField', 'global_settings', 'Year abroad', 'textarea', 'This text will appear under the Year Abroad text on the Modules tab', '');
		$this->add_field('GlobalSettingField', 'global_settings', 'Year in industry', 'textarea', 'This text will appear under the Year in industry text on the Modules tab.', '');
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

	/**
	 * Adds a field to a fields table.
	 * 
	 * @param string $modelname the class of object we are creating. eg. 'GlobalSettingField' or 'Programme_Field'
	 * @param string $tablename the table name we're creating a field for 
	 * @param string $title the title of the field.
	 * @param string $type the type of field.
	 * @param string $hints the hints for the field.
	 * @param string $options the options, particularly used when the field type is select.
	 */
	public function add_field($modelname, $tablename, $title, $type, $hints, $options)
	{
        // define the column name
    	$colname = Str::slug($title, '_');
    	
    	// set up the field object and save it to the _fields table
    	// eg for a Global_Setting object we set up the GlobalSettingField object and save it to the global_settings_fields table
    	$field_object = new $modelname;
        $field_object->field_name = $title;
        $field_object->field_type = $type;
        $field_object->field_description = $hints;
        $field_object->field_meta = $options;
        $field_object->field_initval =  '';
        $field_object->active = 1;
        $field_object->view = 1;
        $field_object->save();
    	$colname .= '_'.$field_object->id;
    	$field_object->colname = $colname;
    	$field_object->save();
		
		// modify the schema for the main table eg global_settings
		// by default columns are varchars unless they've been specified as textareas
		Schema::table($tablename, function($table) use ($colname, $type) {
		
			if ($type=='textarea')
			{
    			$table->text($colname);
			}
			else
			{
				$table->string($colname, 255);
			}
				
		});
		
		// modify the schema for the revisions table eg global_settings_revisions
		// by default columns are varchars unless they've been specified as textareas
		Schema::table($tablename.'_revisions', function($table) use ($colname, $type) {
		
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
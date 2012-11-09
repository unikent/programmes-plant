<?php

class Create_Programmesettings {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		// Create the programme_settings table
		Schema::table('programme_settings', function($table){
			$table->create();
			$table->increments('id');
			$table->string('year',4);
			$table->string('created_by',10);
			$table->string('published_by', 10);
			$table->timestamps();
		});

		// Create the programme_settings_revisions table
		Schema::table('programme_settings_revisions', function($table){
			$table->create();
			$table->increments('id');
			$table->integer("programme_setting_id");
			$table->string('year', 4);
			$table->string('created_by', 10);
			$table->string('status', 15);
			$table->timestamps();

		});

		// Create the programme_settings_fields table
		Schema::table('programme_settings_fields', function($table){
			$table->create();
    		$table->increments('id');
    		$table->string('field_name');
    		$table->string('field_type');
    		$table->string('field_meta');
    		$table->string('field_description');
    		$table->string('field_initval');
    		$table->integer("prefill")->default('0');
			$table->text("placeholder");
    		$table->integer('active');
    		$table->integer('view');
    		$table->string('colname');
    		$table->timestamps();
		});

		// Add some fields in
		$this->add_field('ProgrammeSettingField', 'programme_settings', 'Start', 'text', 'eg 29 September 2014', '');
		$this->add_field('ProgrammeSettingField', 'programme_settings', 'Foundation year', 'textarea', 'This text will appear under the foundation year text on the Modules tab.', '');
		$this->add_field('ProgrammeSettingField', 'programme_settings', 'Module disclaimer', 'textarea', 'This text will appear under each stage on the Modules tab', '');
		$this->add_field('ProgrammeSettingField', 'programme_settings', 'Year in industry', 'textarea', 'This text will appear under the Year in industry text on the Modules tab.', '');
		$this->add_field('ProgrammeSettingField', 'programme_settings', 'Year abroad', 'textarea', 'This text will appear under the Year Abroad text on the Modules tab', '');
		$this->add_field('ProgrammeSettingField', 'programme_settings', 'Careers/employability text', 'textarea', 'This text will appear under the text on the Careers tab', '');
		$this->add_field('ProgrammeSettingField', 'programme_settings', 'Entry requirements', 'textarea', 'This text will appear under the Entry requirements tab', '');
		$this->add_field('ProgrammeSettingField', 'programme_settings', 'Tuition fees', 'textarea', 'This text will appear under the Fees and Funding tab', '');
		$this->add_field('ProgrammeSettingField', 'programme_settings', 'Funding', 'textarea', 'This text will appear under the Fees and Funding tab', '');
		$this->add_field('ProgrammeSettingField', 'programme_settings', 'How to apply', 'textarea', 'This text will appear under the Apply tab', '');
		$this->add_field('ProgrammeSettingField', 'programme_settings', 'Enquiries', 'textarea', 'This text will appear under the Further informtion tab', '');
		$this->add_field('ProgrammeSettingField', 'programme_settings', 'KIS explanatory text', 'textarea', 'This text will appear under the Further informtion tab', '');
		$this->add_field('ProgrammeSettingField', 'programme_settings', 'General disclaimer', 'textarea', 'This text will appear at the footer of each page', '');
		$this->add_field('ProgrammeSettingField', 'programme_settings', 'Institution name', 'textarea', 'University of Kent', '');
		$this->add_field('ProgrammeSettingField', 'programme_settings', 'UKPRN', 'text', '', '');
		$this->add_field('ProgrammeSettingField', 'programme_settings', 'Contributor', 'textarea', '', '');
		$this->add_field('ProgrammeSettingField', 'programme_settings', 'Catalog description', 'textarea', '', '');
		$this->add_field('ProgrammeSettingField', 'programme_settings', 'Provider URL', 'text', 'http://www.kent.ac.uk', '');
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('programme_settings');
		Schema::drop('programme_settings_fields');
		Schema::drop('programme_settings_revisions');
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
	public function add_field($modelname, $tablename, $title, $type, $hints, $options)
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
        $field_object->save();
    	$colname .= '_'.$field_object->id;
    	$field_object->colname = $colname;
    	$field_object->save();
		
		// modify the schema for the main table eg programme_settings
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
		
		// modify the schema for the revisions table eg programme_settings_revisions
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
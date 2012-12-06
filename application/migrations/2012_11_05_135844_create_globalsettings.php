<?php

class Create_GlobalSettings {

	/**
	 * Create the global_settings tables.
	 * 
	 * This table stores global settings such as institution name
	 * 
	 * There are three tables:
	 * 
	 * 1. the global_settings table that stores the current revision of the global variable.
	 * 
	 * 2. the global_settings_fields table. This stores additional fields that can be added to the global_settings table. It is polled from time to time to produce new columns in the global_settings table.
	 * 
	 * 3. the global_settings_revisions table. This stores revisions if the global_settings table. It can also store revisions of all additional fields added.
	 *
	 * @return void
	 */
	public function up()
	{
		// Create the global_settings table
		Schema::table('global_settings', function($table){
			$table->create();
			$table->increments('id');
			$table->string('year',4);
			$table->string('institution',255);
			$table->string('created_by',10);
			$table->string('published_by', 10);
			$table->timestamps();
		});

		// Create the global_settings_revisions table
		Schema::table('global_settings_revisions', function($table){
			$table->create();
			$table->increments('id');
			$table->integer("global_setting_id");
			$table->string('year', 4);
			$table->string('institution', 255);
			$table->string('created_by', 10);
			$table->string('status', 15);
			$table->timestamps();

		});

		// Create the global_settings_fields table
		Schema::table('global_settings_fields', function($table){
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
		$this->add_field('GlobalSettingField', 'global_settings', 'KIS institution id', 'text', '', '');
		$this->add_field('GlobalSettingField', 'global_settings', 'Apply content', 'textarea', '', '');
		$this->add_field('GlobalSettingField', 'global_settings', 'Fees content', 'textarea', '', '');
		$this->add_field('GlobalSettingField', 'global_settings', 'Additional entry requirement information', 'textarea', '', '');
		
		//Section: Xcri-CAP / KIS global fields
		$this->add_field('GlobalSettingField', 'global_settings', 'Institution name', 'text', 'University of Kent', '');
		$this->add_field('GlobalSettingField', 'global_settings', 'UKPRN', 'text', '', '');
		$this->add_field('GlobalSettingField', 'global_settings', 'Contributor', 'textarea', '', '');
		$this->add_field('GlobalSettingField', 'global_settings', 'Catalog description', 'textarea', '', '');
		$this->add_field('GlobalSettingField', 'global_settings', 'Provider description', 'textarea', '', '');
		$this->add_field('GlobalSettingField', 'global_settings', 'Provider URL', 'text', 'http://www.kent.ac.uk', '');
		$this->add_field('GlobalSettingField', 'global_settings', 'Address line 1', 'text', '', '');
		$this->add_field('GlobalSettingField', 'global_settings', 'Address line 2', 'text', '', '');
		$this->add_field('GlobalSettingField', 'global_settings', 'Address line 3', 'text', '', '');
		$this->add_field('GlobalSettingField', 'global_settings', 'Town', 'text', '', '');
		$this->add_field('GlobalSettingField', 'global_settings', 'Email', 'text', '', '');
		$this->add_field('GlobalSettingField', 'global_settings', 'Fax', 'text', '', '');
		$this->add_field('GlobalSettingField', 'global_settings', 'Phone', 'text', '', '');
		$this->add_field('GlobalSettingField', 'global_settings', 'Postcode', 'text', '', '');
		$this->add_field('GlobalSettingField', 'global_settings', 'Location URL', 'text', '', '');
		$this->add_field('GlobalSettingField', 'global_settings', 'Image source', 'text', 'University logo', '');
		$this->add_field('GlobalSettingField', 'global_settings', 'Image title', 'text', 'eg \'University of Kent logo\'', '');
		$this->add_field('GlobalSettingField', 'global_settings', 'Image alt', 'text', 'eg \'University of Kent logo\'', '');
		$this->add_field('GlobalSettingField', 'global_settings', 'Regulations', 'text', 'a link to the University\'s regulations', '');

	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('global_settings');
		Schema::drop('global_settings_fields');
		Schema::drop('global_settings_revisions');
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
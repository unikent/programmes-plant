<?php

class Create_Globalsettings {

	/**
	 * Create the globalsettings tables.
	 * 
	 * This table stores global settings such as institution name
	 * 
	 * There are three tables:
	 * 
	 * 1. the globalsettings table that stores the current revision of the global variable.
	 * 
	 * 2. the globalsettings_fields table. This stores additional fields that can be added to the globalsettings table. It is polled from time to time to produce new columns in the globalsettings table.
	 * 
	 * 3. the globalsettings_revisions table. This stores revisions if the globalsettings table. It can also store revisions of all additional fields added.
	 *
	 * @return void
	 */
	public function up()
	{
		// Create the globalsettings table
		Schema::table('globalsettings', function($table){
			$table->create();
			$table->increments('id');
			$table->string('year',4);
			$table->string('institution',255);
			$table->string('created_by',10);
			$table->string('published_by', 10);
			$table->timestamps();
		});

		// Create the globalsettings_revisions table
		Schema::table('globalsettings_revisions', function($table){
			$table->create();
			$table->increments('id');
			$table->integer("global_id");
			$table->string('year', 4);
			$table->string('institution', 255);
			$table->string('created_by', 10);
			$table->string('status', 15);
			$table->timestamps();

		});

		// Create the globalsettings_fields table
		Schema::table('globalsettings_fields', function($table){
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
		$this->add_field('GlobalSetting', 'globalsettings', 'KIS instition id', 'text', '', '');
		$this->add_field('GlobalSetting', 'globalsettings', 'Apply content', 'textarea', '', '');
		$this->add_field('GlobalSetting', 'globalsettings', 'Fees content', 'textarea', '', '');
		$this->add_field('GlobalSetting', 'globalsettings', 'Additional entry requirement information', 'textarea', '', '');
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('globalsettings');
		Schema::drop('globalsettings_fields');
		Schema::drop('globalsettings_revisions');
	}

	/**
	 * Adds a field to a fields table.
	 * 
	 * @param string $modelname the class of object we are creating. eg. 'GlobalSetting' or 'Programme'
	 * @param string $tablename the table name we're creating a field for 
	 * @param string $title the title of the field.
	 * @param string $type the type of field.
	 * @param string $hints the hints for the field.
	 * @param string $options the options, particularly used when the field type is select.
	 */
	private function add_field($modelname, $tablename, $title, $type, $hints, $options)
	{
        // define the column name
    	$colname = Str::slug($title, '_');
    	
    	// set up the field object and save it to the _fields table
    	// eg for a GlobalSetting object we set up the GlobalSetting_Field object and save it to the globalsetting_field table
    	$model = $modelname.'Field';
    	$field_object = new $model;
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
		
		// modify the schema for the main table eg globalsettings
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
		
		// modify the schema for the revisions table eg globalsettings_revisions
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
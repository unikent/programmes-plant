<?php

Config::set('verify::verify.prefix', 'usersys');

class Add_Pg_Fields {

	public function run(){
		$programme_fields = array();
		$programme_override_fields = array();
		$programme_setting_fields = array();
		

		// title, type, hints, options, section (except setting_fields)

		// standard fields
		// Section: Title
		$programme_fields[] = array('POS code', 'text', 'eg Accounting and Finance and Economics', '', 1);

		// section: key facts
		$programme_fields[] = array('Programme type', 'checkbox', 'Please check the relevant boxes or leave blank if the programme is none of these.', 'taught,research', 2);
		$programme_fields[] = array('Awarding institute or body', 'select', 'University of Kent', 'University of Kent,University of Kent and AUEB-GR', 2);
		$programme_fields[] = array('Administrative school', 'table_select', 'Please select the school with the main administrative responsibility for this programme.', 'School', 2);
		$programme_fields[] = array('Additional school', 'table_select', 'Please select any additional school that this programme is run in conjunction with.', 'School', 2);
		$programme_fields[] = array('Subject area 2', 'table_select', 'Please select the main subject area for this programme if appropriate.', 'Subject', 2);
		$programme_fields[] = array('Location', 'table_select', '', 'Campus', 2);
		$programme_fields[] = array('Mode of study', 'select', '', 'Full-time only, Part-time only, Full-time or part-time', 2);
		$programme_fields[] = array('Attendance mode', 'checkbox', 'Campus', 'Campus, Distance with attendance, Distance without attendance, Mixed', 2);
		$programme_fields[] = array('Attendance pattern', 'checkbox', 'Day-time', 'Day-time, evening, weekend, customized', 2);
		$programme_fields[] = array('Duration', 'text', 'eg 3 years full-time, 6 years part-time', '', 2);
		$programme_override_fields[] = array('Start', 'text', 'Enter when students can start this programme.', '', 2);
		$programme_fields[] = array('Accredited by', 'text', '', '', 2);
		$programme_fields[] = array('Total Kent credits awarded on completion', 'text', 'eg 360', '', 2);
		$programme_fields[] = array('Total ECTS credits awarded on completion', 'text', 'eg 180', '', 2);
		$programme_override_fields[] = array('Entry requirements', 'textarea', '', '', 2);
		
		// section: course structure
		$programme_fields[] = array('Course content', 'textarea', '', '', 3);
		$programme_fields[] = array('Assessment', 'textarea', 'Please give details of how the programme is taught and assessed here. Max 200 words.', '', 3);
		$programme_fields[] = array('Programme aims', 'textarea', '', '', 3);
		$programme_fields[] = array('Knowledge and understanding (learning outcomes)', 'textarea', '', '', 3);
 		$programme_fields[] = array('Intellectual skills (learning outcomes)', 'textarea', '', '', 3);
 		$programme_fields[] = array('Subject-specific skills (learning outcomes)', 'textarea', '', '', 3);
 		$programme_fields[] = array('Transferable skills (learning outcomes)', 'textarea', '', '', 3);

		// section: key information
		$programme_fields[] = array('Resources', 'textarea', 'Please give details of how the resources available to students taking this course. Max 200 words.', '', 4);
		$programme_fields[] = array('Key information (miscellaneous)', 'textarea', 'Please give details of how the career opportunities available to students.', '', 4);

		// section: careers and employability
		$programme_fields[] = array('Careers and employability', 'textarea', 'Please give details of how what career paths students who take this programme may follow. Max 200 words.', '', 5);
		$programme_fields[] = array('Professional recognition', 'textarea', 'Please give details if this course helps students to attain professional status, eg formal recognition by a professional body Max 150 words.', '', 5);


		// section: research
		$programme_fields[] = array('Research groups', 'textarea', 'Please enter details of research groups here.', '', 6);
		$programme_fields[] = array('Staff research interests', 'textarea', '', '', 6);
		

		// section: fees and funding
		$programme_override_fields[] = array('Fees and funding', 'textarea', 'Some standard intro text about fees and funding will output on the web page. If the standard text is not applicable you can override it by writing in this field. Max 200 words.', '', 7);

		// section: how to apply
		$programme_override_fields[] = array('How to apply', 'textarea', 'Please note standard text will appear about how to apply. If the standard text is not applicable, please enter alternative text here.', '', 8);

		// section: further information
		$programme_fields[] = array('Enquiries', 'textarea', 'Please note the standard contact details for admissions enquiries will appear on the webpage. You can add additional contact information here.', '', 9);
		$programme_fields[] = array('Programme leaflet', 'table_select', 'Please select the subject leaflet that contains information about this programme.', 'Leaflet', 9);
		$programme_fields[] = array('Student profile', 'text', 'Please add a complete link to the student profile for this subject eg http://www.kent.ac.uk/courses/postgraduate/profiles/anthropology.html', '', 9);
		$programme_fields[] = array('School website', 'text', '', '', 9);
		$programme_fields[] = array('Staff profiles', 'text', '', '', 9);

		// section: page administration
		$programme_fields[] = array('Programme url', 'text', '', '', 10);
		$programme_fields[] = array('Search keywords', 'text', 'Add search keywords, separated by a comma.', '', 10);
		$programme_fields[] = array('Related courses', 'table_multiselect', 'Please note, the related courses that appear will be all the other programmes related to the subject(s) selected in the ‘Programme title and key facts’ section. You can add additional related courses below.', 'PG_Programme', 10);
		$programme_fields[] = array('Holding message ', 'textarea', 'This field is only to be used when all the content on the page except for the programme title needs to be replaced by a single message. For example, if the course is suspended or removed.', '', 10);
		$programme_fields[] = array('New programme', 'checkbox', 'Check the box if this is a new programme that has been added since the start of the prospectus cycle', '', 10);

		$programme_fields[] = array('Subject Categories', 'table_select', '', 'SubjectCategory', 10);
		$programme_fields[] = array('Module Session', 'text', '', '', 10);


		// immutable fields

		/*Content: Admissions enquiries
T: +44 (0)1227 827272 
E: information@kent.ac.uk*/
		$programme_setting_fields[] = array('Enquiries', 'textarea', '', '');

		//Please also see our general entry requirements (including our English language requirements).
		$programme_setting_fields[] = array('General entry requirements', 'text', '', '');
		
		$programme_setting_fields[] = array('Graduate school', 'textarea', '', '');
		$programme_setting_fields[] = array('Open days', 'text', '', '');
		$programme_setting_fields[] = array('Prospectus link', 'textarea', '', '');

		
		// Add the fields in
		foreach ($programme_fields as $field) {
			$this->add_field('PG_ProgrammeField', array('programme_settings_pg', 'programmes_pg', 'programme_settings_revisions_pg', 'programmes_revisions_pg'), $field[0], $field[1], $field[2], $field[3], PG_ProgrammeField::$types['NORMAL'], $field[4]);
		}

		// Add the global fields in
		foreach ($programme_setting_fields as $field) {
			$this->add_global_field('GlobalSettingField', 'global_settings', $field[0], $field[1], $field[2], $field[3]);
		}


		// Add the fields in
		foreach ($programme_override_fields as $field) {
			$this->add_field('PG_ProgrammeField', array('programme_settings_pg', 'programmes_pg', 'programme_settings_revisions_pg', 'programmes_revisions_pg'), $field[0], $field[1], $field[2], $field[3], PG_ProgrammeField::$types['OVERRIDABLE_DEFAULT'], $field[4]);
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
	public function add_global_field($modelname, $tablename, $title, $type, $hints, $options)
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
        $field_object->raw_save();
    	$colname .= '_'.$field_object->id;
    	$field_object->colname = $colname;
    	$field_object->raw_save();
		
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
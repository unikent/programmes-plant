<?php

class Refactor_Programmes_Fields_And_Settings {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		//1.
		Schema::drop('programme_settings_fields');

		Schema::table('programmes_fields', function($table){
			$table->integer('programme_field_type');
		});

		$this->addProgrammeFields();
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		//1.
		Schema::table('programme_settings_fields', function($table){
			$table->create();
    		$table->increments('id');
    		$table->string('field_name');
    		$table->string('field_type');
    		$table->string('field_meta');
    		$table->string('field_description');
    		$table->string('field_initval');
    		$table->integer('prefill')->default('0');
			$table->text('placeholder');
    		$table->integer('active');
    		$table->integer('view');
    		$table->string('colname');
    		$table->timestamps();
		});

		//2.
		Schema::table('programmes_fields', function($table){

			$table->drop_column('programme_field_type');

		});
	}

	public function addProgrammeFields(){
		$programme_fields = array();
		$programme_override_fields = array();
		$programme_setting_fields = array();
		
		//Section: Programme title and key facts
		$programme_fields[] = array('Programme title', 'text', 'eg Accounting and Finance and Economics', '');
		$programme_fields[] = array('Slug', 'text', 'eg accounting_and_finance_and_economics. This will override the default system generated slug if filled in.', '');
		$programme_fields[] = array('Award', 'table_select', '', 'Award');
		$programme_fields[] = array('Awarding institute or body', 'select', 'University of Kent', 'University of Kent,Medway School of Pharmacy,University of Kent,Canterbury Christ Church University');
		$programme_fields[] = array('Honours type', 'select', '', 'Single honours, Joint honours, European honours');
		$programme_fields[] = array('Administrative school', 'table_select', 'If the programme is joint honours, please select the school with the main administrative responsibility.', 'School');
		$programme_fields[] = array('Additional school', 'table_select', 'For joint honours programmes, please select the other school that this programme is run in conjunction with.', 'School');
		$programme_fields[] = array('Subject area 1', 'table_select', 'Please select the main subject area for this programme.  If the programme is joint honours, please ensure the subject selected is taught by the administrative school.', 'Subject');
		$programme_fields[] = array('Subject area 2', 'table_select', 'If the programme is joint honours, please select the second subject area.', 'Subject');
		$programme_fields[] = array('UCAS code', 'text', '', '');
		$programme_fields[] = array('Location', 'table_select', '', 'Campus');
		$programme_fields[] = array('Mode of study', 'select', '', 'Full-time only, Part-time only, Full-time or part-time');
		$programme_fields[] = array('Attendance mode', 'checkbox', 'Campus', 'Campus, Distance with attendance, Distance without attendance, Mixed');
		$programme_fields[] = array('Attendance pattern', 'checkbox', 'Day-time', 'Day-time, evening, weekend, customized');
		$programme_fields[] = array('Duration', 'text', 'eg 3 years full-time, 6 years part-time', '');
		$programme_override_fields[] = array('Start', 'text', 'By default, the first day of term (eg 29 September 2014) will appear on the page unless you type an alternative start date into this field.', '');
		$programme_fields[] = array('Accredited by', 'text', 'Indicate any professional accreditation.', '');//-----------IN DEFAULTS------------
		$programme_fields[] = array('Programme type', 'checkbox', 'Please check the relevant boxes or leave blank if the programme is none of these.', 'year in industry, year abroad, foundation year');
		$programme_fields[] = array('Total Kent credits awarded on completion', 'text', 'eg 360', '');
		$programme_fields[] = array('Total ECTS credits awarded on completion', 'text', 'eg 180', '');

		//Section: Overview
		$programme_fields[] = array('Programme abstract', 'textarea', 'Please write a concise abstract for this programme. Max 140 characters.  The abstract is not outputted on our course pages but is used for the xcri-cap feed.', '');//140 character limit
		$programme_fields[] = array('Programme overview text', 'text', 'This text should give an overview of the programme and what it offers prospective students. Max 300 words.', '');//max 300 words... shows users when they have reached word limit but does allow over-matter.
		
		//Section: Course Structure (modules tab)
			//This modules by stage information below is read only. Changes to modules cannot be made here but must be made to the v_pos_modules table in SDS. Please ask your SDS administrator to make any changes and they will be updated here on a X basis. 
			//[output for module by stages taken from v_pos_module]
		$programme_fields[] = array('Foundation year', 'textarea', 'If the programme has a Foundation Year, please give details explaining what it entails and how students can benefit. Max 200 words.', ''); // Shows users when they have reached word limit but does allow over-matter. 
		//[note on form] Please note standard text will appear under this tab. See ‘Standard messages’ (pdf). * [Note we could either have a pdf or similar with all the standard text or give admins read-only access to the global settings so they can read the notes)] 
		$programme_fields[] = array('Year in industry', 'textarea', 'If the programme has a year in industry, please give details explaining what it entails and how students can benefit. Max 200 words.', ''); // Function: Shows users when they have reached word limit but does allow over-matter. 
		//[note on form] Please note standard text will appear under this tab. See ‘Standard messages’ (pdf). 
		$programme_fields[] = array('Year abroad', 'textarea', 'If the programme has a year abroad, please give details explaining what it entails and how students can benefit. Max 200 words.', ''); // Function: Shows users when they have reached word limit but does allow over-matter. 
		//[note on form] Please note standard text will appear under this tab. See ‘Standard messages’ (pdf).
 
		//Section: Teaching and Assessment 
		$programme_fields[] = array('Teaching and assessment', 'textarea', 'Please give details of how the programme is taught and assessed here. Max 200 words.', ''); // Function: Shows users when they have reached word limit but does allow over-matter. 
		$programme_fields[] = array('Programme aims', 'textarea', 'Please give details of the programme aims here. Max 200 words.', ''); // Function: Shows users when they have reached word limit but does allow over-matter. 
		$programme_fields[] = array('Learning outcomes', 'textarea', 'Please give details of the learning outcomes here. Max 200 words.', ''); // Function: Shows users when they have reached word limit but does allow over-matter. 

		//Section: Careers 
		$programme_fields[] = array('Careers overview', 'textarea', 'Please give details of how what career paths students who take this programme may follow. Max 200 words.', ''); // Function: Shows users when they have reached word limit but does allow over-matter.
		$programme_fields[] = array('Professional recognition', 'textarea', 'Does this course help students to attain professional status, eg formal recognition by a professional body? With which organisations? Max 150 words.', ''); // Function: Shows users when they have reached word limit but does allow over-matter. 
		
		//[note on form] Please note standard text will appear under this tab. See ‘Standard messages’ (pdf). 

		//Section: Entry requirements 
		$programme_fields[] = array('Entry profile', 'textarea', '', '');
		$programme_fields[] = array('Home/EU students intro text', 'textarea', '', ''); 	
		$programme_fields[] = array('A level', 'textarea', '', '');
		$programme_fields[] = array('CGSE', 'textarea', '', '');
		$programme_fields[] = array('Cambridge Pre-U', 'textarea', '', '');
		$programme_fields[] = array('International Baccalaureate', 'textarea', '', '');
		$programme_fields[] = array('Access to HE Diploma', 'textarea', '', '');
		$programme_fields[] = array('BTEC Level 5 HND', 'textarea', '', '');
		$programme_fields[] = array('BTEC Level 3 Extended Diploma (formerly BTEC National Diploma)', 'textarea', '', '');
		$programme_fields[] = array('Scottish qualifications', 'textarea', '', '');
		$programme_fields[] = array('Irish Leaving Certificate', 'textarea', '', '');
		$programme_fields[] = array('International students intro textarea', 'text', '', '');
		$programme_fields[] = array('Kent International Foundation Programme', 'textarea', '', '');
		$programme_fields[] = array('English language requirements', 'textarea', '', '');
		$programme_fields[] = array('Entry requirements: Overriding text', 'textarea', 'If you enter text into this field it will output instead of any the other entry requirement fields above.', '');
			
		//Please note standard text will appear under this tab. See ‘Standard messages’ (pdf). 

		//Section: Fees and Funding
		$programme_fields[] = array('Tuition fees', 'textarea', 'Please note standard text will appear about tuition fees. See ‘Standard messages’ (pdf). If the standard text is not applicable, please enter alternative text here.', ''); // Function: Overrides the standard message. 
		$programme_override_fields[] = array('Funding', 'textarea', 'Please note standard text will appear about funding and scholarships. See ‘Standard messages’ (pdf). If the standard text is not applicable, please enter alternative text here.', ''); // Function: Overrides the standard message. 


		//Section: How to apply 
		$programme_override_fields[] = array('How to apply', 'textarea', 'Please note standard text will appear about how to apply. See ‘Standard messages’ (pdf). If the standard text is not applicable, please enter alternative text here.', ''); // Function: Overrides the standard message. 

		//Section: Further information 
		$programme_override_fields[] = array('Enquiries', 'textarea', 'Please note standard text will appear about Enquiries.  See ‘Standard messages’ (pdf). If the standard text is not applicable, please enter alternative text here.', ''); // Function: Overrides the standard message. 
		$programme_fields[] = array('Subject leaflet', 'table_select', 'Please select the subject leaflet that contains information about this programme.', 'Leaflet'); 
		$programme_fields[] = array('Subject leaflet 2', 'table_select', 'If the programme is joint honours, please select an additional subject leaflet that contains information about this programme.', 'Leaflet');
		$programme_fields[] = array('Student profile', 'text', 'Please add a complete link to the student profile for this subject eg http://www.kent.ac.uk/courses/undergrad/profiles/american.html', ''); //Type: text [URL]
		$programme_fields[] = array('Student profile 2', 'text', 'If the programme is joint honours, please add a complete link to an additional student profile for this subject.', ''); //Type: text [URL]

		//Open days: Please note standard text will appear about ‘Open Days’. See ‘Standard messages’ (pdf).


		//Section: KIS details 
		$programme_fields[] = array('KISCOURSEID', 'text', 'This is the programme’s KISCOURSEID and is used for the KIS widget.', ''); // Function: Read only to users, editable by superusers.
		$programme_fields[] = array('KISTYPE ', 'text', 'This is the programme’s KISTYPE.', ''); // Function: Read only to users, editable by superusers.
		$programme_override_fields[] = array('KIS explanatory text', 'textarea', 'Please note standard text will appear about the KIS widget. See ‘Standard messages’ (pdf). If the standard text is not applicable, please enter alternative text here.', ''); // Function: Overrides the standard message. 
		$programme_fields[] = array('JACS code (subject 1)', 'text', 'Please add the JACS code for the main subject area', '');
		$programme_fields[] = array('JACS code (subject 2)', 'text', 'Please add the JACS code for the additional subject area', '');

		//Section: Page administration 
			//This area is for the administration of pages conducted by Enrolment management services. If you have any queries or request regarding this section, please contact EMS. 
		$programme_fields[] = array('POS Code', 'text', '', ''); //Type: text 
		$programme_fields[] = array('Programme url', 'text', 'Use this field to change the URL of this programme page. Eg /courses/programmes/accounting-and-finance', ''); //Type: text. Function: Read only to users, editable by superusers.
		$programme_fields[] = array('Search keywords', 'text', 'Add search keywords, separated by a comma', ''); //Type: text 
		$programme_fields[] = array('Subject categories', 'table_multiselect', 'select the subject categories this programme falls into. The programme will appear under these category in the course finder. ', 'SubjectCategory'); //Type: Subject category multi-selection tool. Predefined subject categories. Function:Read only to users, editable by superusers. 
			//NB: The groups will be the all-new broader subject categories that are going to be used in print prospectus. Approx. 30-40 categories. 
		$programme_fields[] = array('Related courses', 'table_multiselect', 'Please note, the related courses that appear will be all the other programmes related to the subject(s) selected in the ‘Programme title and key facts’ section. You can add additional related courses below.', 'Programme'); //Type: multi-selection tool, auto-complete search or similar. Function: Must be able to select multiple related courses. IS to consider options.  Approx 400 programmes to choose from. 
		$programme_fields[] = array('Holding message ', 'textarea', 'This field is only to be used when all the content on the page except for the programme title needs to be replaced by a single message. For example, if the course is suspended or withdrawn. ', ''); // Function: Read only to users, editable by superusers. 
		$programme_fields[] = array('New programme', 'text', 'Check the box if this is a new programme that has been added since the start of the prospectus cycle', ''); //Type: checkbox. Function: Read only to users, editable by superusers. 
		$programme_fields[] = array('Prorgamme specification URL', 'text', '', ''); //Type: URL to programme spec. 
		$programme_fields[] = array('Subject to approval', 'checkbox', 'Check the box if this programme has been approved for advertising subject to approval by PASC. ', ''); //Type: Checkbox. Function: Read only to users, editable by superusers. 
		$programme_fields[] = array('Programme suspended', 'checkbox', 'Only tick the box if the programme has been suspended.  Please unpublish the programme if necessary or add a holding message.', ''); //Type: Checkbox. Function: Read only to users, editable by superusers.
		$programme_fields[] = array('Programme withdrawn', 'checkbox', 'Only tick the box if the programme has been completely withdrawn. Please unpublish the programme if necessary or add a holding message.', ''); //Type: Checkbox. Function: Read only to users, editable by superusers.


		//defaults
		$programme_setting_fields[] = array('Foundation year', 'textarea', 'This text will appear under the foundation year text on the Modules tab.', '');
		$programme_setting_fields[] = array('Module disclaimer', 'textarea', 'This text will appear under each stage on the Modules tab', '');
		$programme_setting_fields[] = array('Year in industry', 'textarea', 'This text will appear under the Year in industry text on the Modules tab.', '');
		$programme_setting_fields[] = array('Year abroad', 'textarea', 'This text will appear under the Year Abroad text on the Modules tab', '');
		$programme_setting_fields[] = array('Careers/employability text', 'textarea', 'This text will appear under the text on the Careers tab', '');
		$programme_setting_fields[] = array('Entry requirements', 'textarea', 'This text will appear under the Entry requirements tab', '');
		$programme_setting_fields[] = array('Tuition fees', 'textarea', 'This text will appear under the Fees and Funding tab', '');
		$programme_setting_fields[] = array('General disclaimer', 'textarea', 'This text will appear at the footer of each page', '');
		$programme_setting_fields[] = array('Institution name', 'textarea', 'University of Kent', '');
		$programme_setting_fields[] = array('UKPRN', 'text', '', '');
		$programme_setting_fields[] = array('Contributor', 'textarea', '', '');
		$programme_setting_fields[] = array('Catalog description', 'textarea', '', '');
		$programme_setting_fields[] = array('Provider URL', 'text', 'http://www.kent.ac.uk', '');
		$programme_setting_fields[] = array('Address line 1', 'text', '', '');
		$programme_setting_fields[] = array('Address line 2', 'text', '', '');
		$programme_setting_fields[] = array('Address line 3', 'text', '', '');
		$programme_setting_fields[] = array('Town', 'text', '', '');
		$programme_setting_fields[] = array('Email', 'text', '', '');
		$programme_setting_fields[] = array('Fax', 'text', '', '');
		$programme_setting_fields[] = array('Phone', 'text', '', '');
		$programme_setting_fields[] = array('Postcode', 'text', '', '');
		$programme_setting_fields[] = array('Location URL', 'text', '', '');
		$programme_setting_fields[] = array('Image source', 'text', 'University logo', '');
		$programme_setting_fields[] = array('Image title', 'text', 'eg \'University of Kent logo\'', '');
		$programme_setting_fields[] = array('Image alt', 'text', 'eg \'University of Kent logo\'', '');
		$programme_setting_fields[] = array('Regulations', 'text', 'a link to the University\'s regulations', '');


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
<?php

class Add_Programme_Data {

    public static $title_list = array(
        array('slug'=>'accounting-and-finance-and-economics', 'title'=>'Accounting and Finance and Economics'),
        array('slug'=>'accounting-and-finance-with-a-year-in-industry', 'title'=>'Accounting and Finance with a Year in Industry'),
        array('slug'=>'accounting-and-finance', 'title'=>'Accounting and Finance'),
        array('slug'=>'accounting-and-management-with-a-year-in-industry', 'title'=>'Accounting and Management with a Year in Industry'),
        array('slug'=>'accounting-and-management', 'title'=>'Accounting and Management'),
        array('slug'=>'actuarial-science-with-a-year-in-industry', 'title'=>'Actuarial Science with a Year in Industry'),
        array('slug'=>'actuarial-science', 'title'=>'Actuarial Science'),
        array('slug'=>'american-studies-history', 'title'=>'American Studies (History)'),
        array('slug'=>'american-studies-latin-america', 'title'=>'American Studies (Latin America)'),
        array('slug'=>'american-studies-literature', 'title'=>'American Studies (Literature)'),
        array('slug'=>'american-studies', 'title'=>'American Studies'),
    );

    public static $input_data_source = array(
        'programme_title_1'=>'',
        'slug_2'=>'',
        'award_3'=>'1',
        'awarding_institute_or_body_4'=>'University of Kent',
        'honours_type_5'=>'Single honours',
        'administrative_school_6'=>'1',
        'additional_school_7'=>'1',
        'subject_area_1_8'=>'1',
        'subject_area_2_9'=>'1',
        'ucas_code_10'=>'1234',
        'location_11'=>'1',
        'mode_of_study_12'=>'Full-time only',
        'attendance_mode_13'=>'false',
        'attendance_pattern_14'=>'false',
        'duration_15'=>'',
        'accredited_by_16'=>'',
        'programme_type_17'=>'',
        'total_kent_credits_awarded_on_completion_18'=>'',
        'total_ects_credits_awarded_on_completion_19'=>'',
        'programme_abstract_20'=>'',
        'programme_overview_text_21'=>'',
        'teaching_and_assessment_22'=>'',
        'programme_aims_23'=>'',
        'learning_outcomes_24'=>'',
        'careers_overview_25'=>'',
        'professional_recognition_26'=>'',  
        'did_you_know_fact_box_27'=>'',
        'a_level_28'=>'',
        'cgse_29'=>'',
        'cambridge_preu_30'=>'',
        'international_baccalaureate_31'=>'',
        'btec_level_5_hnd_32'=>'',
        'btec_level_3_extended_diploma_formerly_btec_national_diploma_33'=>'',
        'scottish_qualifications_34'=>'',
        'irish_leaving_certificate_35'=>'',
        'subject_leaflet_36'=>'',
        'subject_leaflet_2_37'=>'',
        'student_profile_38'=>'',
        'student_profile_2_39'=>'',
        'kiscourseid_40'=>'',
        'kistype_41'=>'',
        'jacs_code_subject_1_42'=>'',
        'jacs_code_subject_2_43'=>'',
        'pos_code_44'=>'',
        'programme_url_45'=>'',
        'search_keywords_46'=>'',
        'subject_categories_47'=>'',
        'related_courses_48'=>'',
        'holding_message_49'=>'',
        'new_programme_50'=>'',
        'prorgamme_specification_url_51'=>'',
        'subject_to_approval_52'=>'',
        'programme_suspended_53'=>'',
        'programme_withdrawn_54'=>'',
        'foundation_year_55'=>'',
        'module_disclaimer_56'=>'',
        'year_in_industry_57'=>'',
        'year_abroad_58'=>'',
        'careersemployability_text_59'=>'',
        'general_disclaimer_60'=>'',
        'start_61'=>'',
        'entry_profile_62'=>'',
        'homeeu_students_intro_text_63'=>'',
        'access_to_he_diploma_64'=>'',
        'international_students_intro_text_65'=>'',
        'kent_international_foundation_programme_66'=>'',
        'english_language_requirements_67'=>'',
        'entry_requirements_overriding_text_68'=>'',
        'tuition_fees_69'=>'',
        'funding_70'=>'',
        'how_to_apply_71'=>'',
        'enquiries_72'=>'',
        'kis_explanatory_textarea_73'=>'',
    );

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
        // get all available fields
        $programme_fields = ProgrammeField::programme_fields();
        // assign values to the fields based on our data source, and save each one
        foreach (self::$title_list as $title_data)
        {   
            // set up the new programme
            $programme = new Programme;
            $programme->year = '2014';
            $programme->created_by = 'at369';
            $programme->live = 1;
            $programme->published_by = 'at369';
            self::$input_data_source['programme_title_1'] = $title_data['title'];
            self::$input_data_source['slug_2'] = $title_data['slug'];
            $programme_modified = ProgrammeField::assign_fields($programme, $programme_fields, self::$input_data_source);
            $programme_modified->save();
        }
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		$programmes = Programme::all();

		foreach ($programmes as $programme)
		{
			$programme->delete();
		}
	}

}
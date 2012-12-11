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
        'foundation_year_22'=>'',
        'year_in_industry_23'=>'',
        'year_abroad_24'=>'',
        'teaching_and_assessment_25'=>'',
        'programme_aims_26'=>'',
        'learning_outcomes_27'=>'',
        'careers_overview_28'=>'',
        'professional_recognition_29'=>'',  
        'did_you_know_fact_box_30'=>'',
        'a_level_31'=>'',
        'cgse_32'=>'',
        'cambridge_preu_33'=>'',
        'international_baccalaureate_34'=>'',
        'btec_level_5_hnd_35'=>'',
        'btec_level_3_extended_diploma_formerly_btec_national_diploma_36'=>'',
        'scottish_qualifications_37'=>'',
        'irish_leaving_certificate_38'=>'',
        'subject_leaflet_39'=>'',
        'subject_leaflet_2_40'=>'',
        'student_profile_41'=>'',
        'student_profile_2_42'=>'',
        'kiscourseid_43'=>'',
        'kistype_44'=>'',
        'jacs_code_subject_1_45'=>'',
        'jacs_code_subject_2_46'=>'',
        'pos_code_47'=>'',
        'programme_url_48'=>'',
        'search_keywords_49'=>'',
        'subject_categories_50'=>'',
        'related_courses_51'=>'',
        'holding_message_52'=>'',
        'new_programme_53'=>'',
        'prorgamme_specification_url_54'=>'',
        'subject_to_approval_55'=>'',
        'programme_suspended_56'=>'',
        'programme_withdrawn_57'=>'',
        'foundation_year_58'=>'',
        'module_disclaimer_59'=>'',
        'year_in_industry_60'=>'',
        'year_abroad_61'=>'',
        'careersemployability_text_62'=>'',
        'general_disclaimer_63'=>'',
        'start_64'=>'',
        'entry_profile_65'=>'',
        'homeeu_students_intro_text_66'=>'',
        'access_to_he_diploma_67'=>'',
        'international_students_intro_text_68'=>'',
        'kent_international_foundation_programme_69'=>'',
        'english_language_requirements_70'=>'',
        'entry_requirements_overriding_text_71'=>'',
        'tuition_fees_72'=>'',
        'funding_73'=>'',
        'how_to_apply_74'=>'',
        'enquiries_75'=>'',
        'kis_explanatory_textarea_76'=>'',
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
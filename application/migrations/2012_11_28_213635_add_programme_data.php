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
        'entry_profile_30'=>'',
        'homeeu_students_intro_text_31'=>'',
        'a_level_32'=>'',
        'cgse_33'=>'',
        'cambridge_preu_34'=>'',
        'international_baccalaureate_35'=>'',
        'access_to_he_diploma_36'=>'',
        'btec_level_5_hnd_37'=>'',
        'btec_level_3_extended_diploma_formerly_btec_national_diploma_38'=>'',
        'scottish_qualifications_39'=>'',
        'irish_leaving_certificate_40'=>'',
        'international_students_intro_textarea_41'=>'',
        'kent_international_foundation_programme_42'=>'',
        'english_language_requirements_43'=>'',
        'entry_requirements_overriding_text_44'=>'',
        'tuition_fees_45'=>'',
        'subject_leaflet_46'=>'',
        'subject_leaflet_2_47'=>'',
        'student_profile_48'=>'',
        'student_profile_2_49'=>'',
        'kiscourseid_50'=>'',
        'kistype_51'=>'',
        'kis_explanatory_text_52'=>'',
        'jacs_code_subject_1_53'=>'',
        'jacs_code_subject_2_54'=>'',
        'pos_code_55'=>'',
        'programme_url_56'=>'',
        'search_keywords_57'=>'',
        'subject_categories_58'=>'',
        'related_courses_59'=>'',
        'holding_message_60'=>'',
        'new_programme_61'=>'',
        'prorgamme_specification_url_62'=>'',
        'subject_to_approval_63'=>'',
        'programme_suspended_64'=>'',
        'programme_withdrawn_65'=>'',
        'start_93'=>'',
        'funding_94'=>'',
        'how_to_apply_95'=>'',
        'enquiries_96'=>'',
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
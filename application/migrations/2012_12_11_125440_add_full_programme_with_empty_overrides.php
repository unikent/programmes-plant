<?php

class Add_Full_Programme_With_Empty_Overrides {
    
        public static $input_data_source = array(
        'duration_15'=>'3 years',
        'accredited_by_16'=>'University of Kent',
        'programme_type_17'=>'year in industry',
        'total_kent_credits_awarded_on_completion_18'=>'200',
        'total_ects_credits_awarded_on_completion_19'=>'100',
        'programme_abstract_20'=>'This is an abstract about the Accounting and Finance and Economics programme',
        'programme_overview_text_21'=>'<div>Accountants are probably best known for checking the validity of company accounts-auditing- but they also devise and operate financial systems, conduct investment analysis, advise on business start-ups, company takeovers and company rescue schemes, and handle individuals\' and corporations\' tax affairs.</div><div><br></div><div>At Kent Business School, we have designed the Accounting and Finance degrees to ensure that they respond to the needs and expectations of the modern accountancy profession. We offer the opportunity to spend a year on work placement, or to combine accounting with related subjects. Our supportive and flexible approach to teaching gives you the confidence and skills you need to follow the path that most interests you.</div><div><br></div><div>We have an excellent record of graduate employment with our graduates moving into a range of careers, including professional training in public practice (chartered accountancy) or in industry, commerce or the public sector, or financial services or general management.</div><div><br></div><div>Our programme is fully accredited by all the professional accountancy bodies and allows you to obtain more exemptions from professional accounting examinations than at most other universities in the UK. For example, we offer a taxation module which confers additional professional exemptions; taxation is not offered at many other UK universities.</div>',
        'foundation_year_22'=>'<div>Some foundation year text</div>',
        'year_in_industry_23'=>'<div>Some year in industry text</div>',
        'year_abroad_24'=>'<div>Some year abroad text</div>',
        'teaching_and_assessment_25'=>'<div>Usually you spend eight hours in lectures and four hours in seminars each week. Some modules have a number of workshops or sessions in computer laboratories. Most of your modules involve individual study using Library resources.</div><div><br></div><div>Most modules have an end-of-year examination that contributes either 70% or 80% to the final module mark: your coursework provides the remaining marks. Both Stage 2 and 3 marks count towards your final degree class (together with your marks from your year in industry, if applicable).</div>',
        'programme_aims_26'=>'<div>The aims of this programme are to teach students how to become accountants.</div>',
        'learning_outcomes_27'=>'<div>The learning outcomes of this programme are to get students to understand how finance works.</div>',
        'careers_overview_28'=>'<div>Kent Business School equips you with the skills you need to build a successful career. Through your studies, you acquire communication skills, the ability to work in a team and independently, and the ability to express your opinions passionately and persuasively. Through our varied contacts in the business world, we give you the opportunity to gain valuable work experience as part of your degree.</div><div><br></div><div>We have an excellent record of graduate employment with recent graduates going into accountancy training with firms such as KPMG, Ernst &amp;amp; Young and PricewaterhouseCoopers, other financial services with banks or private companies, or other types of management such as recruitment or marketing.</div><div><br></div><div>For more information on the services Kent provides to improve your employment prospects,&nbsp;visit <a target="_blank" rel="nofollow" href="http://www.kent.ac.uk/employability">www.kent.ac.uk/employability</a></div><div><br></div><div>Full or partial exemption from the preliminary stage of professional accountancy examinations provided you choose the appropriate modules. Single honours degrees offer further exemptions from the examinations of some accountancy bodies.</div>',
        'professional_recognition_29'=>'<div>This course is recognised by all the major accountancy bodies.</div>',
        'did_you_know_fact_box_30'=>'Some did you know fact box text.',
        'a_level_31'=>'<div>4 A-levels at grade A.</div>',
        'cgse_32'=>'<div>10 GCSEs.</div>',
        'cambridge_preu_33'=>'<div>Cambridge pre-u text.</div>',
        'international_baccalaureate_34'=>'<div>International baccalaureate text.</div>',
        'btec_level_5_hnd_35'=>'<div>BTEC level 5 HND text.</div>',
        'btec_level_3_extended_diploma_formerly_btec_national_diploma_36'=>'<div>BTEC level 3 extended text.</div>',
        'scottish_qualifications_37'=>'<div>Scottish equivalent text.</div>',
        'irish_leaving_certificate_38'=>'<div>Irish leaving certificate text.</div>',
        'subject_leaflet_39'=>'http://www.kent.ac.uk/leaflet1',
        'subject_leaflet_2_40'=>'http://www.kent.ac.uk/leaflet2',
        'student_profile_41'=>'http://www.kent.ac.uk/studentprofile1',
        'student_profile_2_42'=>'http://www.kent.ac.uk/studentprofile2',
        'kiscourseid_43'=>'ACCF-ECON_BA_LN14',
        'kistype_44'=>'kistype',
        'jacs_code_subject_1_45'=>'JACSCODE1',
        'jacs_code_subject_2_46'=>'JACSCODE2',
        'pos_code_47'=>'LN14',
        'programme_url_48'=>'',
        'search_keywords_49'=>'accounting, finance, business, KBS, economics',
        'subject_categories_50'=>'',
        'related_courses_51'=>'2,1',
        'holding_message_52'=>'',
        'new_programme_53'=>'',
        'prorgamme_specification_url_54'=>'http://www.kent.ac.uk/programmespec',
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
        $programme = Programme::find(1);
        $programme->created_by = 'at369';
        $programme->published_by = 'at369';
        $programme_modified = ProgrammeField::assign_fields($programme, $programme_fields, self::$input_data_source);
        $programme_modified->save();
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

}
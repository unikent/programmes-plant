<?php
Config::set('verify::verify.prefix', 'usersys');

use \Verify\Models\Permission;

class Set_Field_Permissions {

	static $fields = array(
		array('section_name' => 'Key facts','colname' => 'award_3','section' => '1', 'user_permissions' => array('read','write')),
		array('section_name' => 'Key facts','colname' => 'awarding_institute_or_body_4','section' => '1', 'user_permissions' => array('read','write')),
		array('section_name' => 'Key facts','colname' => 'honours_type_5','section' => '1', 'user_permissions' => array('read','write')),
		array('section_name' => 'Key facts','colname' => 'administrative_school_6','section' => '1', 'user_permissions' => array('read','write')),
		array('section_name' => 'Key facts','colname' => 'additional_school_7','section' => '1', 'user_permissions' => array('read','write')),
		array('section_name' => 'Key facts','colname' => 'subject_area_1_8','section' => '1', 'user_permissions' => array('read','write')),
		array('section_name' => 'Key facts','colname' => 'subject_area_2_9','section' => '1', 'user_permissions' => array('read','write')),
		array('section_name' => 'Key facts','colname' => 'ucas_code_10','section' => '1', 'user_permissions' => array('read','write')),
		array('section_name' => 'Key facts','colname' => 'location_11','section' => '1', 'user_permissions' => array('read','write')),
		array('section_name' => 'Key facts','colname' => 'mode_of_study_12','section' => '1', 'user_permissions' => array('read','write')),
		array('section_name' => 'Key facts','colname' => 'attendance_mode_13','section' => '1', 'user_permissions' => array('read','write')),
		array('section_name' => 'Key facts','colname' => 'attendance_pattern_14','section' => '1', 'user_permissions' => array('read','write')),
		array('section_name' => 'Key facts','colname' => 'duration_15','section' => '1', 'user_permissions' => array('read','write')),
		array('section_name' => 'Key facts','colname' => 'accredited_by_16','section' => '1', 'user_permissions' => array('read','write')),
		array('section_name' => 'Key facts','colname' => 'programme_type_17','section' => '1', 'user_permissions' => array('read','write')),
		array('section_name' => 'Key facts','colname' => 'total_kent_credits_awarded_on_completion_18','section' => '1', 'user_permissions' => array('read','write')),
		array('section_name' => 'Key facts','colname' => 'total_ects_credits_awarded_on_completion_19','section' => '1', 'user_permissions' => array('read','write')),
		array('section_name' => 'Key facts','colname' => 'start_61','section' => '1', 'user_permissions' => array('read','write')),
		array('section_name' => 'Key facts','colname' => 'url_for_administrative_school_74','section' => '1', 'user_permissions' => array('read','write')),
		array('section_name' => 'Key facts','colname' => 'url_for_additional_school_75','section' => '1', 'user_permissions' => array('read','write')),
		array('section_name' => 'Overview','colname' => 'programme_abstract_20','section' => '2', 'user_permissions' => array('read','write')),
		array('section_name' => 'Overview','colname' => 'programme_overview_text_21','section' => '2', 'user_permissions' => array('read','write')),
		array('section_name' => 'Course Structure','colname' => 'foundation_year_78','section' => '3', 'user_permissions' => array('read','write')),
		array('section_name' => 'Course Structure','colname' => 'year_in_industry_79','section' => '3', 'user_permissions' => array('read','write')),
		array('section_name' => 'Course Structure','colname' => 'year_abroad_80','section' => '3', 'user_permissions' => array('read','write')),
		array('section_name' => 'Teaching and Assessment','colname' => 'teaching_and_assessment_22','section' => '4', 'user_permissions' => array('read','write')),
		array('section_name' => 'Teaching and Assessment','colname' => 'programme_aims_23','section' => '4', 'user_permissions' => array('read','write')),
		array('section_name' => 'Teaching and Assessment','colname' => 'learning_outcomes_24','section' => '4', 'user_permissions' => array('read','write')),
		array('section_name' => 'Teaching and Assessment','colname' => 'intellectual_skills_learning_outcomes_82','section' => '4', 'user_permissions' => array('read','write')),
		array('section_name' => 'Teaching and Assessment','colname' => 'subjectspecific_skills_learning_outcomes_83','section' => '4', 'user_permissions' => array('read','write')),
		array('section_name' => 'Teaching and Assessment','colname' => 'transferable_skills_learning_outcomes_84','section' => '4', 'user_permissions' => array('read','write')),
		array('section_name' => 'Careers','colname' => 'careers_overview_25','section' => '5', 'user_permissions' => array('read','write')),
		array('section_name' => 'Careers','colname' => 'professional_recognition_26','section' => '5', 'user_permissions' => array('read','write')),
		array('section_name' => 'Careers','colname' => 'did_you_know_fact_box_27','section' => '5', 'user_permissions' => array('read','write')),
		array('section_name' => 'Entry requirements ','colname' => 'a_level_28','section' => '6', 'user_permissions' => array('read','write')),
		array('section_name' => 'Entry requirements ','colname' => 'cgse_29','section' => '6', 'user_permissions' => array('read','write')),
		array('section_name' => 'Entry requirements ','colname' => 'international_baccalaureate_31','section' => '6', 'user_permissions' => array('read','write')),
		array('section_name' => 'Entry requirements ','colname' => 'btec_level_5_hnd_32','section' => '6', 'user_permissions' => array('read','write')),
		array('section_name' => 'Entry requirements ','colname' => 'btec_level_3_extended_diploma_formerly_btec_national_diploma_33','section' => '6', 'user_permissions' => array('read','write')),
		array('section_name' => 'Entry requirements ','colname' => 'entry_profile_62','section' => '6', 'user_permissions' => array('read','write')),
		array('section_name' => 'Entry requirements ','colname' => 'homeeu_students_intro_text_63','section' => '6', 'user_permissions' => array('read','write')),
		array('section_name' => 'Entry requirements ','colname' => 'access_to_he_diploma_64','section' => '6', 'user_permissions' => array('read','write')),
		array('section_name' => 'Entry requirements ','colname' => 'international_students_intro_text_65','section' => '6', 'user_permissions' => array('read','write')),
		array('section_name' => 'Entry requirements ','colname' => 'kent_international_foundation_programme_66','section' => '6', 'user_permissions' => array('read','write')),
		array('section_name' => 'Entry requirements ','colname' => 'english_language_requirements_67','section' => '6', 'user_permissions' => array('read','write')),
		array('section_name' => 'Entry requirements ','colname' => 'entry_requirements_overriding_text_68','section' => '6', 'user_permissions' => array('read','write')),
		array('section_name' => 'Fees and Funding','colname' => 'tuition_fees_69','section' => '7', 'user_permissions' => array('read','write')),
		array('section_name' => 'Fees and Funding','colname' => 'funding_70','section' => '7', 'user_permissions' => array('read','write')),
		array('section_name' => 'How to apply','colname' => 'how_to_apply_71','section' => '8', 'user_permissions' => array('read','write')),
		array('section_name' => 'Further information','colname' => 'subject_leaflet_36','section' => '9', 'user_permissions' => array('read')),
		array('section_name' => 'Further information','colname' => 'subject_leaflet_2_37','section' => '9', 'user_permissions' => array('read')),
		array('section_name' => 'Further information','colname' => 'student_profile_38','section' => '9', 'user_permissions' => array('read')),
		array('section_name' => 'Further information','colname' => 'student_profile_2_39','section' => '9', 'user_permissions' => array('read')),
		array('section_name' => 'Further information','colname' => 'enquiries_72','section' => '9', 'user_permissions' => array('read','write')),
		array('section_name' => 'Further information','colname' => 'student_profile_1_link_text_76','section' => '9', 'user_permissions' => array('read')),
		array('section_name' => 'Further information','colname' => 'student_profile_2_link_text_77','section' => '9', 'user_permissions' => array('read')),
		array('section_name' => 'KIS details','colname' => 'kiscourseid_40','section' => '10', 'user_permissions' => array()),
		array('section_name' => 'KIS details','colname' => 'jacs_code_subject_1_42','section' => '10', 'user_permissions' => array()),
		array('section_name' => 'KIS details','colname' => 'jacs_code_subject_2_43','section' => '10', 'user_permissions' => array()),
		array('section_name' => 'KIS details','colname' => 'kis_explanatory_textarea_73','section' => '10', 'user_permissions' => array()),
		array('section_name' => 'Page administration','colname' => 'slug_2','section' => '11', 'user_permissions' => array()),
		array('section_name' => 'Page administration','colname' => 'search_keywords_46','section' => '11', 'user_permissions' => array()),
		array('section_name' => 'Page administration','colname' => 'subject_categories_47','section' => '11', 'user_permissions' => array()),
		array('section_name' => 'Page administration','colname' => 'related_courses_48','section' => '11', 'user_permissions' => array()),
		array('section_name' => 'Page administration','colname' => 'holding_message_49','section' => '11', 'user_permissions' => array()),
		array('section_name' => 'Page administration','colname' => 'new_programme_50','section' => '11', 'user_permissions' => array()),
		array('section_name' => 'Page administration','colname' => 'subject_to_approval_52','section' => '11', 'user_permissions' => array()),
		array('section_name' => 'Page administration','colname' => 'programme_suspended_53','section' => '11', 'user_permissions' => array()),
		array('section_name' => 'Page administration','colname' => 'programme_withdrawn_54','section' => '11', 'user_permissions' => array()),
		array('section_name' => 'Programme title and POS code','colname' => 'programme_title_1','section' => '12', 'user_permissions' => array('read','write')),
		array('section_name' => 'Programme title and POS code','colname' => 'pos_code_44','section' => '12', 'user_permissions' => array('read')),
		array('section_name' => 'Programme title and POS code','colname' => 'module_session_86','section' => '12', 'user_permissions' => array())
	);
	
	/**
	 * Run the permissions seed. Here's how
	 * php artisan seed set-field-permissions --env=[environment]
	 */
	function run()
	{
		$this->add_specified_roles();
	}

	/**
	 * Assigns all permissions in the systems to users and admins
	 */
	function add_all_permissions_to_admin_and_user_roles(){
		$permissions = Permission::all();
		
		foreach ($permissions as $permission) {
			$permission->roles()->sync(array(2,3));
		}
	}

	/**
	 * Assigns all permissions in the systems to admins, and only the specified ones to users
	 */
	function add_specified_roles(){

		foreach (static::$fields as $field) {
			$read_permission = Permission::where_name('fields_read_' . $field['colname'])->first();
			$write_permission = Permission::where_name('fields_write_' . $field['colname'])->first();
			
			$user_can_read = in_array('read', $field['user_permissions']);
			$user_can_write = in_array('write', $field['user_permissions']);

			$read_roles = $user_can_read ? array(2,3) : array(2);
			$write_roles = $user_can_write ? array(2,3) : array(2);

			if ($user_can_read) echo "adding user to fields_read_{$field['colname']} permission\n";
			
			$read_permission->roles()->sync($read_roles);

			if ($user_can_write) echo "adding user to fields_write_{$field['colname']} permission\n";
			$write_permission->roles()->sync($write_roles);
		}
	}
	
}
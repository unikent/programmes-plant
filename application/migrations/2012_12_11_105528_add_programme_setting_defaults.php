<?php

class Add_Programme_Setting_Defaults {
    
    public static $input_data_source = array(
        'start_64'=>'September 29 2014',        
        'entry_profile_65'=>'<div>Entry profile text.</div>',
        'homeeu_students_intro_text_66'=>'<div>Home/EU students are welcome.</div>',
        'access_to_he_diploma_67'=>'<div>Access to HE diploma text.</div>',
        'international_students_intro_text_68'=>'<div>International students intro text.</div>',
        'kent_international_foundation_programme_69'=>'<div>Kent international foundation programme text.</div>',
        'english_language_requirements_70'=>'<div>English language requirements text.</div>',
        'entry_requirements_overriding_text_71'=>'Achieving an average mark of 60% on the Kent IFP (International Foundation Programme) guarantees you entry onto the first year of these degree programmes. This does not include marks obtained at resits of Foundation examinations.<h5>Offer levels</h5>ABB at A level, IB Diploma 33 points inc 4 in Mathematics (5 in Mathematics Studies) or IB Diploma with 16 points at Higher inc 4 in Mathematics (5 in Mathematics Studies).<h5>Required subjects</h5>GCSE Mathematics grade B.',
        'tuition_fees_72'=>'<div>Tuition fees text.</div>',
        'funding_73'=>'<div>Some funding text for Accounting &amp; Finance</div>',
        'how_to_apply_74'=>'<div>How to apply text</div>',
        'enquiries_75'=>'<div>T: +44 (0)1227 827272<br>E: information@kent.ac.uk</div>',
        'kis_explanatory_textarea_76'=>'<div>The Key Information Set (KIS) data (right) is compiled by UNISTATS and draws from a variety of sources which includes the National Student Survey and the Higher Education Statistical Agency. The data for assessment and contact hours is compiled from the most populous modules (to the total of 120 credits for an academic session) for this particular degree programme. Depending on module selection, there may be some variation between the KIS data and an individual\'s experience. For further information on how the KIS data is compiled please see the <a href="http://unistats.direct.gov.uk/find-out-more/key-information-set/">UNISTATS website</a>.</div><div><br></div><div>If you have any queries about a particular programme, please contact <a href="mailto:information@kent.ac.uk">information@kent.ac.uk</a></div>',
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
        $programme = new ProgrammeSetting;
        $programme->year = '2014';
        $programme->created_by = 'at369';
        $programme->published_by = 'at369';
        foreach (self::$input_data_source as $key => $value)
        {
            $programme->$key = $value;
        }
        //$programme_modified = ProgrammeField::assign_fields($programme, $programme_fields, self::$input_data_source);
        $programme->save();
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
	}

}
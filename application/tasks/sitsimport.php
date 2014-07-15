<?php

//require_once path('base') . 'vendor/autoload.php';

class SITSImport_Task {

    /**
     * Import programme data from SITS
     * 
     * @param array  $arguments The arguments sent to the moduledata command.
     */
    public function run($arguments = array())
    {
        $courses = simplexml_load_file(path('base') . 'storage/data/ipp_web_dev-test.xml');
        $seen_programmes = array();
        
        foreach ($courses as $course) {
            $course_id = substr($course->programmePlantID, 0, count($course->programmePlantID) - 3);
            $course_attendance_pattern = strtolower($course->attendanceType);
            $course_level = '';
            $current_ipo = '';
            $previous_ipo = '';
            $current_ipo_column_name = 'current_ipo';
            $previous_ipo_column_name = 'previous_ipo';
            $programme = false;
            $programme_model = "";
            $ipos = array();
            foreach ($course->ipo as $ipo) {
                $ipos[] = $ipo;
            }

            // set ug specific vars
            if (strpos(strtolower($course->programmePlantID), 'ug') !== false ) {
                $course_level = 'ug';
                $current_ipo_column_name = 'current_ipo_pt';
                $previous_ipo_column_name = 'previous_ipo_pt';
                $programme_model = "UG_Programme";
            }

            // set pg specific vars
            elseif (strpos(strtolower($course->programmePlantID), 'pg') !== false ) {
                $course_level = 'pg';
                $programme_model = "PG_Programme";
            }

            //get the associated programme
            $programme = $programme_model::where('instance_id', '=', $course_id)->where('year', '=', Setting::get_setting($course_level . "_current_year"))->first();

            // only continue if the programme is found
            if (!empty($programme)) {

                switch ($course_level) {
                    case 'ug':
                        // this only applies to part time courses
                        if ($course_attendance_pattern == 'part-time') {
                            $this->set_ipo($programme, $ipos, $current_ipo_column_name, $previous_ipo_column_name);
                            $programme->save();
                        }
                        
                        break;

                    case 'pg':
                        URLParams::$type = 'pg';
                        // blits the deliveries for this programme if its the first time we're encountering it
                        if (!in_array($course_id, $seen_programmes)) {
                            foreach ($programme->get_deliveries() as $delivery) {
                                $delivery->delete();
                            }
                        }

                        $delivery = new PG_Deliveries;
                        $delivery->programme_id = $course_id;

                        $award = PG_Award::where('longname', '=', $course->award)->first();
                        $delivery->award = !empty($award) ? $award->id : 0;

                        $delivery->pos_code = $course->pos;
                        $delivery->mcr = $course->mcr;
                        $delivery->description = $course->description;
                        $delivery->attendance_pattern = $course_attendance_pattern;

                        $this->set_ipo($programme, $ipos, $current_ipo_column_name, $previous_ipo_column_name, $delivery);

                        $delivery->save();

                        break;
                    default:
                        # code...
                        break;
                }
                
            }

            // add this programme to a list of seen programmes
            if (!in_array($course_id, $seen_programmes)) {
                $seen_programmes[] = $course_id;
            }
            
        }

        echo "Done!\n";
    }

    public function set_ipo($programme, $ipos, $current_ipo_column_name, $previous_ipo_column_name, $delivery = false){
        $current_ipo_is_set = false;
        $previous_ipo_is_set = false;
        $mcr_object = !empty($delivery) ? $delivery : $programme;

        foreach ($ipos as $ipo) {
            if (!$current_ipo_is_set && intval($ipo->academicYear) - 1 === intval($programme->year)) {
                //print_r($mcr_object);
                $mcr_object->$current_ipo_column_name = $ipo->sequence;
                //$mcr_object->save();
                $current_ipo_is_set = true;
            }
            elseif (!$previous_ipo_is_set && intval($ipo->academicYear) - 1 === intval($programme->year) - 1) {
                //print_r($mcr_object);
                $mcr_object->$previous_ipo_column_name = $ipo->sequence;
                //$mcr_object->save();
                $previous_ipo_is_set = true;
            }

            // if both IPOs are set the exit the
            if ($current_ipo_is_set && $previous_ipo_is_set) {
                break;
            }
        }
    }
    
}
    
    
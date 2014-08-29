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
        // clear out the api output cache completely so we can regenerate the cache now including the new module data
        try
        {
            Cache::purge('api-output-pg');
            Cache::purge('api-output-ug');
        }
        catch(Exception $e)
        {
            echo 'No cache to purge';
        }
        
        $courses = simplexml_load_file('/www/live/shared/shared/data/SITSCourseData/SITSCourseData.xml');
        $seen_programmes = array();
        
        foreach ($courses as $course) {
            if ($course->progID == '') {
                continue;
            }
            $course_id = substr($course->progID, 0, count($course->progID) - 3);
            $course_attendance_pattern = strtolower($course->attendanceType);
            $course_level = '';
            $current_ipo_column_name = 'current_ipo';
            $previous_ipo_column_name = 'previous_ipo';
            $programme = false;
            $programme_model = "";
            $ipos = array();
            foreach ($course->ipo as $ipo) {
                $ipos[] = $ipo;
            }

            // set ug specific vars
            if (strpos(strtolower($course->progID), 'ug') !== false ) {
                $course_level = 'ug';
                $current_ipo_column_name = 'current_ipo_pt';
                $previous_ipo_column_name = 'previous_ipo_pt';
                $programme_model = "UG_Programme";
            }

            // set pg specific vars
            elseif (strpos(strtolower($course->progID), 'pg') !== false ) {
                $course_level = 'pg';
                $programme_model = "PG_Programme";
            }

            //get the associated programme
            $programme = $programme_model::where('instance_id', '=', $course_id)->where('year', '=', '2015'/*Setting::get_setting($course_level . "_current_year")*/)->first();
            
            $year = '2015';//Setting::get_setting($course_level . "_current_year");

            // only continue if the programme is found
            if ( !empty($programme) && is_object($programme) ) {

                $programme_id = $programme->id;

                switch ($course_level) {
                    case 'ug':
                        // this only applies to part time courses
                        if ($course_attendance_pattern == 'part-time') {
                            $revisions = $programme->get_revisions();
                            $this->set_values($programme, $revisions, "$course->mcr", "$course->pos", $year, $ipos, $current_ipo_column_name, $previous_ipo_column_name, "$course->ari_code");
                            $programme->parttime_mcr_code_87 = "$course->mcr";
                            $programme->pos_code_44 = "$course->pos";
                            $programme->ari_code = "$course->ari_code";
                            $programme->raw_save();
                        }
                        elseif ($course_attendance_pattern == 'full-time') {
                            $programme->fulltime_mcr_code_88 = "$course->mcr";
                            $programme->pos_code_44 = "$course->pos";
                            $programme->ari_code = "$course->ari_code";
                            $programme->raw_save();
                            $revisions = $programme->get_revisions();
                            foreach ($revisions as $revision) {
                                $revision->fulltime_mcr_code_88 = "$course->mcr";
                                $revision->pos_code_44 = "$course->pos";
                                $revision->ari_code = "$course->ari_code";
                                $revision->save();
                            }
                        }
                        
                        break;

                    case 'pg':
                        URLParams::$type = 'pg';
                        // blitz the deliveries for this programme if its the first time we're encountering it
                        if (!in_array($programme_id, $seen_programmes)) {
                            $seen_programmes[] = $programme_id;
                            foreach ($programme->get_deliveries() as $delivery) {
                                $delivery->delete();
                            }
                        }

                        $delivery = new PG_Deliveries;
                        $delivery->programme_id = $programme_id;

                        $award = PG_Award::where('longname', '=', $course->award)->first();
                        $delivery->award = !empty($award) ? $award->id : 0;

                        $delivery->pos_code = "$course->pos";
                        $delivery->mcr = "$course->mcr";
                        $delivery->ari_code = "$course->ari_code";
                        $delivery->description = "$course->description";
                        $delivery->attendance_pattern = $course_attendance_pattern;

                        $this->set_values($programme, array(), $course->mcr, $course->pos, $year, $ipos, $current_ipo_column_name, $previous_ipo_column_name, "$course->ari_code", $delivery);

                        $delivery->save();

                        break;
                    default:
                        # code...
                        break;
                }

                $revision = $programme->find_live_revision();
                if ( !empty($revision) && is_object($revision) ) $programme_model::generate_api_programme($revision->instance_id, $year, $revision);
                
            }
            
        }

        echo "Done!\n";
    }

    public function set_values($programme, $revisions, $mcr, $pos, $year, $ipos, $current_ipo_column_name, $previous_ipo_column_name, $ari_code, $delivery = false){

        Auth::login(1);

        $current_ipo_is_set = false;
        $previous_ipo_is_set = false;

        // ug keeps mcr+ipo data at the programme level. pg keeps it in a delivery object
        if (!empty($delivery)) {

            // go through all the ipos and get the first one in the current year
            foreach ($ipos as $ipo) {
                if (!$current_ipo_is_set && intval($ipo->academicYear) - 1 === intval($programme->year)) {
                    $delivery->$current_ipo_column_name = (string)$ipo->sequence;
                    $current_ipo_is_set = true;
                }
                elseif (!$previous_ipo_is_set && intval($ipo->academicYear) - 1 === intval($programme->year) - 1) {
                    $delivery->$previous_ipo_column_name = (string)$ipo->sequence;
                    $previous_ipo_is_set = true;
                }
                else {
                    continue;
                }

                // if both IPOs are set the exit the loop
                if ($current_ipo_is_set && $previous_ipo_is_set) {
                    break;
                }
            }
        }
        // ug - only part-time is relevant here
        else {

            // if there are no ipos for this course, we still want to set the ari_code and other bits
            // sort out all the revisions too
            if (empty($ipos)) {
                foreach ($revisions as $revision) {
                    $revision->ari_code = $ari_code;
                    $revision->parttime_mcr_code_87 = $mcr;
                    $revision->pos_code_44 = $pos;
                    $revision->save();
                }
            }
            else {
                // go through all the ipos and get the first one in the current year
                foreach ($ipos as $ipo) {

                    // make sure we set the right ipo column for current and previous years
                    $colname = '';
                    if (!$current_ipo_is_set && intval($ipo->academicYear) - 1 === intval($programme->year)) {
                        $colname = $current_ipo_column_name;
                        $current_ipo_is_set = true;
                    }
                    elseif (!$previous_ipo_is_set && intval($ipo->academicYear) - 1 === intval($programme->year) - 1) {
                        $colname = $previous_ipo_column_name;
                        $previous_ipo_is_set = true;
                    }
                    else {
                        continue;
                    }

                    // sort out all the revisions too
                    $programme->$colname = (string)$ipo->sequence;
                    foreach ($revisions as $revision) {
                        $revision->ari_code = $ari_code;
                        $revision->$colname = (string)$ipo->sequence;
                        $revision->parttime_mcr_code_87 = $mcr;
                        $revision->pos_code_44 = $pos;
                        $revision->save();
                    }

                    // if both IPOs are set the exit the loop
                    if ($current_ipo_is_set && $previous_ipo_is_set) {
                        break;
                    }
                }
            }
        }

    }
    
}
    
    
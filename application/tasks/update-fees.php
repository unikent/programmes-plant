<?php

class Update_fees_Task {

    /**
     * Generate the fee data
     * 
     * @param array  $arguments The arguments sent to the moduledata command.
     */
    public function run($arguments = array())
    {
        $year = isset($arguments[0]) ? $arguments[0] : "current";

         // If its just the one...
        $years = array($year);

        // If the one is "current"
        if($year == 'current'){

            // If current we may have differing UG/PG years, so we may need to
            // load two years in at this point

            $ug_year = Setting::get_setting("ug_current_year");
            $pg_year = Setting::get_setting("pg_current_year");

            if($ug_year==$pg_year){
                // The same, so just load once
                $years = array($ug_year);
            }else{
                // Load both
                $years = array($ug_year, $pg_year);
            }
        }

        echo "\nGenerating fee mappings for: \n\n";

        // Regen fee data for year
        foreach($years as $y){
            Fees::generate_fee_map($y);

            echo "- {$y} \n";
        }

        echo "\nDone :)";

    }
    
}
    
    
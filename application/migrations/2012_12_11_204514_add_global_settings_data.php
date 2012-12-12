<?php

class Add_Global_Settings_Data {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
	    Schema::table('global_settings', function($table){	
			$table->drop_column('institution');
		});
		
        $global_setting = new GlobalSetting;
        $global_setting->year = '2014';	 	 	 		
        $global_setting->created_by = 'at369';		 	 	 	 	 	 		
        $global_setting->published_by = 'at369';		 	 	 	 	 	 			 	 	 	 	 	 		
        $global_setting->institution_name_1 = 'University of Kent';		 	 	 	 	 	 		
        $global_setting->ukprn_2 = '10007150';		 	 	 	 	 	 		
        $global_setting->contributor_3 = '';		 	 	 					
        $global_setting->catalog_description_4 = '';		 	 	 					
        $global_setting->provider_description_5 = '';		 	 	 					
        $global_setting->provider_url_6 = 'http://www.kent.ac.uk';		 	 	 	 	 	 		
        $global_setting->address_line_1_7 = 'The University of Kent';		 	 	 	 	 	 		
        $global_setting->address_line_2_8 = 'The Registry';		 	 	 	 	 	 		
        $global_setting->address_line_3_9 = '';		 	 	 	 	 	 		
        $global_setting->town_10 = 'Canterbury';		 	 	 	 	 	 		
        $global_setting->email_11 = '';		 	 	 	 	 	 		
        $global_setting->fax_12 = '';		 	 	 	 	 	 		
        $global_setting->phone_13 = '+44 (0)1227 764000';		 	 	 	 	 	 		
        $global_setting->postcode_14 = 'CT2 7NZ';		 	 	 	 	 	 		
        $global_setting->location_url_15 = '';		 	 	 	 	 	 		
        $global_setting->image_source_16 = '';		 	 	 	 	 	 		
        $global_setting->image_title_17 = '';		 	 	 	 	 	 		
        $global_setting->image_alt_18 = '';		 	 	 	 	 	 		
        $global_setting->regulations_19 = '';		 	 	 	 	 	 		
        $global_setting->live = 1;
        $global_setting->save();
	 
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
	    Schema::table('global_settings', function($table){
			$table->boolean('institution');
		});
		
		$global_setting = GlobalSetting::find(1);
		$global_setting->delete();
	}

}
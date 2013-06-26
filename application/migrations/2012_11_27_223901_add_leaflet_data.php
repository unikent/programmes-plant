<?php

class Add_Leaflet_Data {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{

        DB::table("leaflets")->insert(
			array(
				'name'=> 'Test leaflet',
				'tracking_code' => 'http://www.kent.ac.uk',
				'campuses_id' => '1'
			 )
		);
	}
	
	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{

		DB::table("leaflets")->where(1,'=',1)->delete();
	}

}
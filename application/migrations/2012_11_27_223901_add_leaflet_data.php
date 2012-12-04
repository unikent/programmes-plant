<?php

class Add_Leaflet_Data {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
        $leaflet = new Leaflet;
        $leaflet->name = 'Test leaflet';
        $leaflet->campuses_id = 1;
        $leaflet->tracking_code = 'http://www.kent.ac.uk';
        $leaflet->main = 1;
        $leaflet->save();
	}
	
	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
    	$leaflets = Leaflet::all();
		foreach ($leaflets as $leaflet)
		{
			$leaflet->delete();
		}
	}

}
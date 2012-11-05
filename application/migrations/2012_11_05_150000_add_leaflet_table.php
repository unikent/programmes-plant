<?php

class Add_Leaflet_Table {

	/**
	 * Add leaflet table to database.
	 * 
	 * This centrally stores leaflets, thus making their attachment to programmes easier.
	 *
	 * @return void
	 */
	public function up()
	{		
		//Add leaflets table.
		Schema::table('leaflets', function($table){
			$table->create();
    		$table->increments('id');
    		$table->timestamps();
    		$table->string("name");
    		$table->integer("campuses_id");
    		$table->string("tracking_code");
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{	
		Schema::drop('leaflets');
	}

}
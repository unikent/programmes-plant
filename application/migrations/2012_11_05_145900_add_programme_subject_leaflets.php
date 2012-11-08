<?php

class Add_Programme_Subject_Leaflets{

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		
		Schema::table('programmes', function($table){	
			$table->string('leaflet_url', 255);
			$table->text('additional_leaflet_urls');
		});
		Schema::table('programme_revisions', function($table){
			$table->string('leaflet_url', 255);
			$table->text('additional_leaflet_urls');
		});


	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down(){

		Schema::table('programmes', function($table){	
			$table->drop_column(array('leaflet_url', 'additional_leaflet_urls'));
		});
		Schema::table('programme_revisions', function($table){
			$table->drop_column(array('leaflet_url', 'additional_leaflet_urls'));
		});

	}

	
}
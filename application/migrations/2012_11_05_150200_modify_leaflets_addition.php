<?php

class Modify_Leaflets_Addition {

	/**
	 * Change leaflets reference in programmes to allow multiple IDs.
	 *
	 * @return void
	 */
	public function up()
	{

		Schema::table('programmes', function($table){
			$table->string('leaflet_ids',255);
			$table->drop_column(array('leaflet_url', 'additional_leaflet_urls'));
		});

		Schema::table('programme_revisions', function($table){
			$table->string('leaflet_ids',255);
			$table->drop_column(array('leaflet_url', 'additional_leaflet_urls'));
		});

	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{	

		Schema::table('programmes', function($table){	
			$table->drop_column('leaflet_ids');
			$table->string('leaflet_url', 255);
			$table->text('additional_leaflet_urls');
		});

		Schema::table('programme_revisions', function($table){	
			$table->drop_column('leaflet_ids');
			$table->string('leaflet_url', 255);
			$table->text('additional_leaflet_urls');
		});

	}

}
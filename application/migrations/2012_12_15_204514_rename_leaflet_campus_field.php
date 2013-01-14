<?php

class rename_leaflet_campus_field {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
                Schema::table('leaflets', function($table){
                         $table->integer('campus');
                         $table->drop_column('campuses_id');
                });
	 
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('leaflets', function($table){
                        $table->integer('campuses_id');
                        $table->drop_column('campus');
                });
	}

}
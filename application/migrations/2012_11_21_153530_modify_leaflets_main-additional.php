<?php

class Modify_Leaflets_Main_Additional {

	/**
	 * Change leaflets reference in programmes to allow main vs additional switch
	 *
	 * @return void
	 */
	public function up()
	{

		Schema::table('leaflets', function($table){
			$table->boolean('main');
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
			$table->drop_column('main');
		});

	}

}
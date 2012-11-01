<?php

class Schema_Changes {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
	
		
		Schema::table('programmes', function($table){
			$table->drop_column("honours");
		});
		Schema::table('programmes_revisions', function($table){
			$table->drop_column("honours");
		});
		Schema::table('subjects_revisions', function($table){
			$table->drop_column("created_by");
		});
		//Add meta tables
		Schema::table('programmes', function($table){
    		$table->string("published_by",10);
			$table->integer("honours");
		});

		Schema::table('programmes_revisions', function($table){
			$table->integer("honours");
		});

		Schema::table('subjects_revisions', function($table){
			$table->string("created_by",10);

		});


	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{	

	}

}
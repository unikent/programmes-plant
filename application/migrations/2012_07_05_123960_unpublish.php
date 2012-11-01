<?php

class Unpublish{

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
	
		// Create the subjects table
		Schema::table('programmes', function($table){	
			$table->integer('live');
		});
		Schema::table('subjects', function($table){	
			$table->integer('live');
		});
		Schema::table('supersubjects', function($table){	
			$table->integer('live');
		});
		Schema::table('globals', function($table){	
			$table->integer('live');
		});
		
		//Add award demo data
		$tmp = new Award;
		$tmp->name = 'BSc (Hons)';
		$tmp->save();
		$tmp = new Award;
		$tmp->name = 'BA (Hons)';
		$tmp->save();
		$tmp = new Award;
		$tmp->name = 'Msc';
		$tmp->save();
		$tmp = new Award;
		$tmp->name = 'MA';
		$tmp->save();


	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down(){


	}

	
}
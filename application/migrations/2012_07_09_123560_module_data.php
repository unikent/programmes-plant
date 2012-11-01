<?php

class Module_Data{

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
	
		// Create the subjects table
		Schema::table('programmes', function($table){	
			$table->string('mod_1_title',255);
			$table->text('mod_1_content');
			$table->string('mod_2_title',255);
			$table->text('mod_2_content');
			$table->string('mod_3_title',255);
			$table->text('mod_3_content');
			$table->string('mod_4_title',255);
			$table->text('mod_4_content');
			$table->string('mod_5_title',255);
			$table->text('mod_5_content');
		});
		Schema::table('programmes_revisions', function($table){	
			$table->string('mod_1_title',255);
			$table->text('mod_1_content');
			$table->string('mod_2_title',255);
			$table->text('mod_2_content');
			$table->string('mod_3_title',255);
			$table->text('mod_3_content');
			$table->string('mod_4_title',255);
			$table->text('mod_4_content');
			$table->string('mod_5_title',255);
			$table->text('mod_5_content');
		});




	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down(){


	}

	
}
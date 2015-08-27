<?php

class Add_Jacs_Codes_To_Subject_Areas {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('subjects_ug', function($table){
			
			$table->string('jacs_codes');

		});

		Schema::table('subjects_pg', function($table){
			
			$table->string('jacs_codes');

		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('subjects_ug', function($table){
			
			$table->drop_column('jacs_codes');
			
		});

		Schema::table('subjects_pg', function($table){
			
			$table->drop_column('jacs_codes');
			
		});
	}

}
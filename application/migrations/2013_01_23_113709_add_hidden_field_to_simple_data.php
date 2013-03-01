<?php

class Add_Hidden_Field_To_Simple_Data {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('awards', function($table){
			$table->boolean('hidden');
		});
		Schema::table('campuses', function($table){
			$table->boolean('hidden');
		});
		Schema::table('faculties', function($table){
			$table->boolean('hidden');
		});
		Schema::table('leaflets', function($table){
			$table->boolean('hidden');
		});
		Schema::table('schools', function($table){
			$table->boolean('hidden');
		});
		Schema::table('subjects', function($table){
			$table->boolean('hidden');
		});
		Schema::table('subjectcategories', function($table){
			$table->boolean('hidden');
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('awards', function($table){
			$table->drop_column('hidden');
		});
		Schema::table('campuses', function($table){
			$table->drop_column('hidden');
		});
		Schema::table('faculties', function($table){
			$table->drop_column('hidden');
		});
		Schema::table('leaflets', function($table){
			$table->drop_column('hidden');
		});
		Schema::table('schools', function($table){
			$table->drop_column('hidden');
		});
		Schema::table('subjects', function($table){
			$table->drop_column('hidden');
		});
		Schema::table('subjectcategories', function($table){
			$table->drop_column('hidden');
		});
	}

}
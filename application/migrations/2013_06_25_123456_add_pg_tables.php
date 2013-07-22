<?php

class Add_Pg_Tables {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('awards_pg', function($table){
			$table->increments('id');
			$table->timestamps();

			$table->string('name', 200);
			$table->string('longname', 255);

			$table->integer('hidden');
		});
		Schema::create('leaflets_pg', function($table){
			$table->increments('id');
			$table->timestamps();

			$table->string('name', 255);
			$table->string('tracking_code', 255);
			$table->integer('campus');
			$table->integer('hidden');
		});
		Schema::create('subjectcategories_pg', function($table){
			$table->increments('id');
			$table->timestamps();

			$table->string('name', 255);

			$table->integer('hidden');
		});
		Schema::create('subjects_pg', function($table){
			$table->increments('id');
			$table->timestamps();

			$table->string('name', 255);
			
			$table->integer('hidden');
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('awards_pg');
		Schema::drop('leaflets_pg');
		Schema::drop('subjectcategories_pg');
		Schema::drop('subjects_pg');
	}

}
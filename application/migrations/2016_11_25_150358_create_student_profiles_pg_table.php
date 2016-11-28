<?php

class Create_Student_Profiles_PG_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('student_profiles_pg', function($table) {
			$table->increments('id');
			$table->string('name');
			$table->string('slug');
			$table->string('course');
			$table->integer('banner_image_id');
			$table->integer('profile_image_id');
			$table->integer('subject_categories');
			$table->text('quote');
			$table->string('video');
			$table->text('lead');
			$table->text('content');
			$table->text('links');
			$table->integer('hidden');
			$table->timestamps();

		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('student_profiles_pg');
	}

}
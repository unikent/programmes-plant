<?php

class Create_Student_Profiles_UG_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('student_profiles_ug', function($table) {
			$table->increments('id');
			$table->string('name');
			$table->string('slug');
			$table->string('course');
			$table->integer('banner_image_id')->nullable();
			$table->integer('profile_image_id')->nullable();
			$table->string('subject_categories');
			$table->text('quote');
			$table->string('video');
			$table->text('lead');
			$table->text('content');
			$table->text('links');
			$table->boolean('hidden')->default(0);
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
		Schema::drop('student_profiles_ug');
	}

}
<?php

class Add_Interview_Date_To_Profiles {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		foreach(array('student_profiles_pg', 'student_profiles_ug') as $table_name ) {
			Schema::table($table_name, function($table){
				$table->integer('interview_year')->nullable();
				$table->integer('interview_month')->nullable();
			});
		}
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		foreach(array('student_profiles_pg', 'student_profiles_ug') as $table_name ) {
			Schema::table($table_name, function ($table) {
				$table->drop_column("interview_year");
				$table->drop_column("interview_month");
			});
		}
	}

}
<?php

class Add_Student_Type_Field_Ug {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('student_profiles_ug', function($table) {
			$table->string('type');
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('student_profiles_ug', function($table) {
			$table->drop_column('type');
		});
	}

}
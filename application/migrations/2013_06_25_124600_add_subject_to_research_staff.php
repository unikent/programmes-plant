<?php
class Add_Subject_to_Research_staff {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{

		Schema::table('research_staff', function($table){
			
			$table->integer('subject');

		});

	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('research_staff', function($table){
			
			$table->drop_column('subject');
			
		});
	}
}
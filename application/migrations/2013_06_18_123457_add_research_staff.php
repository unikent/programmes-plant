<?php
class Add_Research_staff {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{

		Schema::create('research_staff', function($table){
			$table->increments('id');
			$table->timestamps();

			$table->string('login', 10);

			$table->string('title', 200);
			$table->string('forename', 200);
			$table->string('surname', 200);
			$table->string('email', 200);

			$table->string('profile_url', 255);
			$table->text('blurb');

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
		Schema::drop('research_staff');
	}
}
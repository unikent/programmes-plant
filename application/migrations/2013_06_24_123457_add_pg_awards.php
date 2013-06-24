<?php
class Add_PG_awards {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{

		Schema::create('pg_programme_deliveries', function($table){
			$table->increments('id');
			$table->timestamps();

			$table->string('award', 255);

			$table->string('pos_code', 200);
			$table->string('attendance_pattern', 255);
			$table->string('mcr', 255);
			$table->integer('programme_id');

		});

	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pg_programme_deliveries');
	}
}
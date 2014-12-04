<?php

class Ug_Deliveries {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('programmes_ug', function($table){
			$table->drop_column('ari_code');
			$table->drop_column('ft_ari_code');
			$table->drop_column('current_ipo_pt');
			$table->drop_column('previous_ipo_pt');
		});

		Schema::table('programmes_revisions_ug', function($table){
			$table->drop_column('ari_code');
			$table->drop_column('ft_ari_code');
			$table->drop_column('current_ipo_pt', 4);
			$table->drop_column('previous_ipo_pt', 4);
		});

		Schema::create('ug_programme_deliveries', function($table){
			$table->increments('id');
			$table->timestamps();

			$table->string('award', 255);

			$table->string('pos_code', 200);
			$table->string('attendance_pattern', 255);
			$table->string('mcr', 255);
			$table->integer('programme_id');

			$table->string('description', 255);

			$table->string('current_ipo', 4);
			$table->string('previous_ipo', 4);
			$table->string('ari_code', 12);
		});

	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('programmes_ug', function($table){
			$table->string('ari_code', 12);
			$table->string('ft_ari_code', 12);
			$table->string('current_ipo_pt', 4);
			$table->string('previous_ipo_pt', 4);
		});

		Schema::table('programmes_revisions_ug', function($table){
			$table->string('ari_code', 12);
			$table->string('ft_ari_code', 12);
			$table->string('current_ipo_pt', 4);
			$table->string('previous_ipo_pt', 4);
		});

		Schema::drop('ug_programme_deliveries');
	}

}
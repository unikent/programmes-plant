<?php

class Add_More_Fields_To_Campuses {

	/**
	 * Add additional fields to campuses.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('campuses', function($table){
			$table->string('title');
			$table->text('description');
			$table->string('address_1');
			$table->string('address_2');
			$table->string('address_3');
			$table->string('town');
			$table->string('email');
			$table->string('phone');
			$table->string('fax');
			$table->string('postcode');
			$table->string('url');
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('campuses', function($table){
			$table->drop_column('title');
			$table->drop_column('description');
			$table->drop_column('address_1');
			$table->drop_column('address_2');
			$table->drop_column('address_3');
			$table->drop_column('town');
			$table->drop_column('email');
			$table->drop_column('phone');
			$table->drop_column('fax');
			$table->drop_column('postcode');
			$table->drop_column('url');
		});
	}

}
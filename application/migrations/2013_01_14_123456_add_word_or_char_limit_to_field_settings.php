<?php

class Add_word_or_char_limit_to_field_settings {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('programmes_fields', function($table){
			$table->string('limit');
		});
		Schema::table('global_settings_fields', function($table){
			$table->string('limit');
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('programmes_fields', function($table){
			$table->drop_column('limit');
		});
		Schema::table('global_settings_fields', function($table){
			$table->drop_column('limit');
		});
	}

}
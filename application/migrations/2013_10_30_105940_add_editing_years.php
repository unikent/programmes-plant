<?php

class Add_editing_years {

		/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('system_settings', function($table){	
			$table->string('ug_editing_year', 200);
			$table->string('pg_editing_year', 200);
		});

		DB::table("system_settings")->where('id','=','1')->update(array('ug_editing_year'=>'2014' ,'pg_editing_year'=>'2014'));

	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('system_settings', function($table){	
			$table->drop_column('ug_editing_year');
			$table->drop_column('pg_editing_year');
		});
	}

}
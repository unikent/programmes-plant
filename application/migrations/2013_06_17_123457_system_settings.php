<?php
Config::set('verify::verify.prefix', 'usersys');
class System_Settings {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{

		Schema::create('system_settings', function($table){
			$table->increments('id');
			$table->timestamps();
			$table->string('ug_current_year', 200);
			$table->string('pg_current_year', 200);
		});
		// Add data
		DB::table("system_settings")->insert(array('ug_current_year'=>'2013' ,'pg_current_year'=>'2013'));

		// Required perm "system_settings", is not added to any group at this point since only
		// hyper admins (who have it by default) will want access anyway in the current setup.
		Permission::create(array('name' => "system_settings"));
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{

		Schema::drop('system_settings');

		$perm = Permission::where('name','=','system_settings')->first();
		$perm->delete();
	}
}
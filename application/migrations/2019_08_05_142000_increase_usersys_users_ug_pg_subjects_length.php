<?php

class Increase_Usersys_Users_Ug_Pg_Subjects_Length {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		// sqlite (used for testing) does not support modifying columns
		if(DB::connection()->config['driver'] !== 'sqlite') {
			DB::query('ALTER TABLE `usersys_users` MODIFY COLUMN `ug_subjects` VARCHAR(10000)');
			DB::query('ALTER TABLE `usersys_users` MODIFY COLUMN `pg_subjects` VARCHAR(10000)');
		}
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		if(DB::connection()->config['driver'] !== 'sqlite') {
			DB::query('ALTER TABLE `usersys_users` MODIFY COLUMN `ug_subjects` VARCHAR(256)');
			DB::query('ALTER TABLE `usersys_users` MODIFY COLUMN `pg_subjects` VARCHAR(256)');
		}
	}

}
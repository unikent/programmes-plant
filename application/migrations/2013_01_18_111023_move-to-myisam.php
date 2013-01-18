<?php

class Move_To_Myisam {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{	
		if (Request::env() == 'test')
		{
			echo "Migration not run - environment is test".PHP_EOL;
			return;
		}

		foreach(array('programmes', 'programmes_revisions') as $table)
		{
			echo "Converting $table to MyISAM...";
			DB::query("ALTER TABLE `$table` ENGINE = MyISAM");
			echo 'done'.PHP_EOL;
		}
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		if (Request::env() == 'test')
		{
			echo "Migration not run - environment is test".PHP_EOL;
			return;
		}

		foreach(array('programmes', 'programmes_revisions') as $table)
		{
			echo "Reverting $table to InnoDB...";
			DB::query("ALTER TABLE `$table` ENGINE = InnoDB");
			echo 'done'.PHP_EOL;
		}
	}

}
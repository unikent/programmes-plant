<?php

class Add_Locking_To_Programmes {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('programmes', function($table)
		{
			$table->text("locked_to");
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('programmes', function($table)
		{
			$table->drop_column("locked_to");
		});
	}

}
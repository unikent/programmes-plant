<?php

class Create_Module_Data {

	/**
	 * Create 5 Module Data Fields In The Database.
	 *
	 * @return void
	 */
	public function up()
	{
		// Don't repeat yourself.
		$number = 1;

		while ($number <= 5) {
			$GLOBALS['field'] = "mod_$number";

			Schema::table('programmes', function($table){
				$field = $GLOBALS['field'];
				$table->string($field . "_title");
				$table->text($field . "_content");
			});

			Schema::table('programmes_revisions', function($table){
				$field = $GLOBALS['field'];
				$table->string($field . "_title");
				$table->text($field . "_content");
			});

			$number++;
		}
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		// Don't *ever* repeat yourself.
		$number = 1;

		while ($number <= 5) {
			$GLOBALS['field'] = "mod_$number";

			Schema::table('programmes', function($table){
				$field = $GLOBALS['field'];
				$table->drop_column($field . "_title");
				$table->drop_column($field . "_content");
			});

			Schema::table('programmes_revisions', function($table){
				$field = $GLOBALS['field'];
				$table->drop_column($field . "_title");
				$table->drop_column($field . "_content");
			});

			$number++;
		}
	}

	
}
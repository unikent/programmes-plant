<?php

class Add_MaterialChange_And_Changelog_To_Revisions {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		foreach(array('programmes_revisions_pg', 'programmes_revisions_ug') as $table_name ) {
			Schema::table($table_name, function($table){
				$table->boolean('material_change')->nullable();
				$table->string('changelog',4096)->nullable();
			});
		}
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		foreach(array('programmes_revisions_pg', 'programmes_revisions_ug') as $table_name ) {
			Schema::table($table_name, function($table){
				$table->dropColumn('material_change');
				$table->dropColumn('changelog');
			});
		}
	}

}
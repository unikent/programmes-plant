<?php

class Remove_Institution_Field_From_Globals {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('global_settings', function($table){	
			$table->drop_column('institution');
		});

		Schema::table('global_settings_revisions', function($table){	
			$table->drop_column('institution');
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('global_settings', function($table){	
			$table->string('institution',255);
		});

		Schema::table('global_settings_revisions', function($table){	
			$table->string('institution',255);
		});
	}

}
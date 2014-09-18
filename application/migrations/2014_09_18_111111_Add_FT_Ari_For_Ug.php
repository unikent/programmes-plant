<?php

class Add_FT_Ari_For_Ug {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('programmes_revisions_ug', function($table){
			$table->string('ft_ari_code', 12);
		});

		Schema::table('programmes_ug', function($table){
			$table->string('ft_ari_code', 12);
		});		
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('programmes_revisions_ug', function($table){
			$table->drop_column('ft_ari_code');
		});

		Schema::table('programmes_ug', function($table){
			$table->drop_column('ft_ari_code');
		});
	}

}
<?php

class Rename_Data_Tables {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::rename('awards',			'awards_ug');
		Schema::rename('leaflets',			'leaflets_ug');
		Schema::rename('subjects', 			'subjects_ug');
		Schema::rename('subjectcategories',	'subjectcategories_ug');
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::rename('awards_ug',				'awards');
		Schema::rename('leaflets_ug',			'leaflets');
		Schema::rename('subjects_ug', 			'subjects');
		Schema::rename('subjectcategories_ug',	'subjectcategories');
	}

}
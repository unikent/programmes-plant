<?php

class Add_Faculty_Data {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		// Add faculty demo data
		foreach (array('Humanities', 'Sciences', 'Social Sciences') as $faculty)
		{
	        DB::table('faculties')->insert(array('name'=> $faculty));
		}
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::table('faculties')->where('id','=','*')->delete();
	}

}
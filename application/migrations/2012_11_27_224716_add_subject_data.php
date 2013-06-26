<?php

class Add_Subject_Data {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{

		DB::table("subjects")->insert(
			array(
				'name'=> 'Architecture'
			 )
		);

	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::table("subjects")->where(1,'=',1)->delete();
	}

}
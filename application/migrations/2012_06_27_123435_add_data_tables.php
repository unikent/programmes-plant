<?php

class Add_Data_Tables {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
	
		
		
		//Add meta tables
		Schema::table('schools', function($table){
			$table->create();
    		$table->increments('id');
    		$table->timestamps();
    		$table->string("name");
    		$table->integer("faculties_id");

		});
		Schema::table('faculties', function($table){
			$table->create();
    		$table->increments('id');
    		$table->timestamps();
    		$table->string("name");

		});
		Schema::table('campuses', function($table){
			$table->create();
    		$table->increments('id');
    		$table->timestamps();
    		$table->string("name");

		});
		
		Schema::table('programmes_revisions', function($table){
    		$table->string("status");
    		$table->integer("programme_id");
		});

$query = <<<SQL

INSERT INTO `campuses` (`id`, `name`)
VALUES
	(1,'Canterbury'),
	(2,'Medway'),
	(3,'Brussels');

SQL;

DB::query($query);

$query = <<<SQL

INSERT INTO `schools` (`id`, `name`)
VALUES
	(1,'Demo School 1'),
	(2,'Demo School 2'),
	(3,'Demo School 3');

SQL;

DB::query($query);

	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{	
		Schema::drop('campuses');
		Schema::drop('faculties');
		Schema::drop('schools');
	}

}
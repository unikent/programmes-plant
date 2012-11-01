<?php

class Recreate_Tables {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		// Create the roles table
		Schema::table('roles', function($table){
			$table->create();
			$table->increments('id');
			$table->string('username');
			$table->string('fullname');
			$table->boolean('isadmin');
			$table->boolean('isuser');
			$table->string('department');
			$table->timestamps();
			$table->index('username');
		});

		// Create the subjects table
		Schema::table('subjects', function($table){
			$table->create();
			$table->increments('id');

			$table->string('title',255);
			$table->string('slug',255);

			$table->text('factbox');
			$table->text('summary');

			$table->string('year',4);
			$table->string('created_by',10);
			$table->timestamps();

			$table->index('id');
			$table->index('year');
		});

		// Create the subject revisions table
		Schema::table('subjects_revisions', function($table){
			$table->create();
			$table->increments('id');
			$table->integer('subject_id');

			$table->string('title',255);
			$table->string('slug',255);

			$table->text('summary');
			$table->text('factbox');

			$table->string('year',4);
			$table->integer('created_by');
			$table->timestamps();

			$table->index('id');
			
			// Currently disabled.
			//$table->foreign('subject_id')->references('id')->on('subjects');
		});

		$role = new Role;
        $role->username = 'at369';
        $role->fullname = "Admin";
        $role->isadmin  = true;
        $role->isuser   = true;
        $role->department = 'IS';
        $role->save();
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('roles');
		Schema::drop('subjects_revisions');
		Schema::drop('subjects');
	}

}
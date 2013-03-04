<?php

class Init {

	public function __construct()
	{
		if (!defined('VERIFY_PREFIX'))
		{
			define('VERIFY_PREFIX', 'usersys_');
		}
	}

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{

		Schema::create(VERIFY_PREFIX.'permissions', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name', 100)->index();
			$table->string('description', 255)->nullable();
			$table->timestamps();
		});

		Schema::create(VERIFY_PREFIX.'roles', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name', 100)->index();
			$table->string('description', 255)->nullable();
			$table->integer('level');
			$table->timestamps();
		});

		Schema::create(VERIFY_PREFIX.'users', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('username', 30)->index();
			$table->string('email', 255)->index();
			$table->string('fullname', 255);
			$table->integer('role_id')->unsigned()->index();
			$table->boolean('verified');
			$table->boolean('disabled');
			$table->boolean('deleted');
			$table->timestamps();
			if(Request::env() != 'test'){
				$table->foreign('role_id')->references('id')->on(VERIFY_PREFIX.'roles');
			}
		});

		Schema::create(VERIFY_PREFIX.'permission_role', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('permission_id')->unsigned()->index();
			$table->integer('role_id')->unsigned()->index();
			$table->timestamps();
			if(Request::env() != 'test'){
				$table->foreign('permission_id')->references('id')->on(VERIFY_PREFIX.'permissions');
				$table->foreign('role_id')->references('id')->on(VERIFY_PREFIX.'roles');
			}
		});

		DB::table(VERIFY_PREFIX.'roles')->insert(array(
			'name'				=> "Administrator",
			'level'				=> 10,
			'created_at'        => date('Y-m-d H:i:s'),
			'updated_at'        => date('Y-m-d H:i:s')
		));

		DB::table(VERIFY_PREFIX.'users')->insert(array(
			'username'			=> 'admin',
			'role_id'			=> 1,
			'email' 			=> 'example@gmail.com',
			'created_at'		=> date('Y-m-d H:i:s'),
			'updated_at'        => date('Y-m-d H:i:s'),
			'verified'			=> 1,
			'disabled'			=> 0,
			'deleted'			=> 0
		));
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop(VERIFY_PREFIX.'permission_role');
		Schema::drop(VERIFY_PREFIX.'users');
		Schema::drop(VERIFY_PREFIX.'roles');
		Schema::drop(VERIFY_PREFIX.'permissions');
	}

}
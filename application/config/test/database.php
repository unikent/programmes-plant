<?php

return array(
	'profile' => true,
	'fetch' => PDO::FETCH_CLASS,
	'default' => 'travis',

	'connections' => array(
		/**
		 * The application database.
		 */
		'sqlite' => array(
			'driver' => 'sqlite',
			'database' => ':memory:',
			'prefix' => '',
		),

		/**
		 * Travis CI
		 */
		'travis' => array(
			'driver'   => 'mysql',
			'host'     => '127.0.0.1',
			'database' => 'programmes',
			'username' => 'root',
			'password' => '',
			'charset'  => 'utf8',
			'prefix'   => '',
		),
	),
);
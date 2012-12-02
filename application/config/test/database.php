<?php

return array(
	'profile' => true,
	'fetch' => PDO::FETCH_CLASS,
	'default' => 'sqlite',

	'connections' => array(
		/**
		 * The application database.
		 */
		'sqlite' => array(
			'driver' => 'sqlite',
			'database' => ':memory:',
			'prefix' => '',
		),
	),
);
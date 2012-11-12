<?php

return array(
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
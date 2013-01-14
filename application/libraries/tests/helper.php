<?php namespace Tests;

use Laravel\CLI\Command;

/**
 * A set of helper functions for running Laravel tests.
 */
class Helper {

	/**
	 * Run all migrations to prepare database for testing.
	 */
	public static function migrate()
	{
		// If there is not a declaration that migrations have been run
		if (!isset($GLOBALS['migrated_test_database']))
		{
			require path('sys').'cli/dependencies'.EXT;

			try
			{
				Command::run(array('migrate:install'));

				// Do this because migration installations do not.
				echo PHP_EOL;
			}
			catch(Exception $e)
			{
				echo PHP_EOL . 'Migration table already installed.';
			}

			// Declare that migrations have been run.
			$GLOBALS['migrated_test_database'] = true;

			Command::run(array('migrate'));

			
		}
	}

	/**
	 * Enable use of sessions for tests.
	 */
	public static function use_sessions()
	{
		\Session::started() or \Session::load();
	}
	
}
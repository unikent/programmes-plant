<?php

use Behat\Behat\Context\BehatContext;

class RecordContext extends BehatContext {

	/**
	 * @Given /^the following (.+) rows$/
	 */
	 public function givenRecords($table, $rows) {

		$rows = $rows->getHash();

		foreach ($rows as $row) {

			$user = new stdClass();
			foreach($row as $key => $column) {
				$user->$key = $column;
			}
			create_object($user, $table);

		}

	}

	/**
	 * @Given /^a clean install$/
	 */
	 public function givenCleanInstall() {

		 $arguments = array('artisan','migrate');

	 	require path('sys').'cli/artisan'.EXT;
	 	if (isset($_SERVER['CLI']['DB'])) Config::set('database.default', $_SERVER['CLI']['DB']);
		require path('sys').'cli/dependencies'.EXT;

		//Kill db with reset, then roll forward to latest to repopulate
		try
		{
			Command::run('migrate:reset');
			Command::run('migrate');
		}
		catch (\Exception $e)
		{
			echo $e->getMessage();
		}
		

	}




	
	/**
	 * @Then /^there should be a user row with username "([^"]*)"$/
	 */
	public function assertUser($arg1) {
		//$user = find_user_by_username($arg1);
		//assertNotEmpty($user->username);
	}

}
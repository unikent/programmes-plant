# Contributing

We welcome contributions from outside developers to the Programmes Plant. Before sending a pull request its best to ensure the following standards are met.

As closely as possible we follow the coding standards laid down by the [Laravel core codebase](https://github.com/laravel/laravel) in the 3.0 series.

## Branches

Pull requests should be made to the `develop` branch. `master` is reserved for stable code. 

Branch names should use a "directory" appropriate to the type of change it is: `feature/`, `bug/`, `refactor/`, `tests/` and so on.

## Code Style

### General Code

As much as possible we mirror the coding style of the Laravel code base (3.0 series). If in doubt, check the code base. We therefore indent using tabs with a tab width of 4. Failure for a method should return null rather than false.

```php
<?php

class Some_Class extends Something {

	/**
	 * Note the new line starting and ending this class.
	 * 
	 * @var array
	 */
	public $some_variable;

	/**
	 * An example method of the Laravel 3.0 coding style.
	 *
	 * A simple verbose description should tell you everything you need to know.
	 *
	 * Note the underscored method name.
	 *
	 * <code>
	 * 	// Echo out an example. Should be indented with a tab.
	 * 	echo $an_example;
	 * </code>
	 *
	 * @param string $thing Some thing.
	 * @param int $status The status to pretend to return.
	 * @return Response Either 404 or some message.
	 */
	public function some_method($thing, $status = 200)
	{
		// PHP 5.3 arrays not allowed since our servers don't support them. :(
		$things = array($thing);

		// No inline comments but comments like this.
		$blah = true;

		// Slightly longer comments
		// work like this on multiple lines.
		if ($thing == true)
		{
			foreach($things as $thing)
			{
				echo "hello world $thing";
			}
		}
		elseif
		{
			echo "Doing something else.";
		}

		// Variable names should be sensibly verbose.
		$xcri_feed = 'thing';

		// Examples zippy one liners.
		if (is_null($connection)) $connection = Config::get('database.default');
		if (is_null($query)) $query = '\Laravel\Database\Query\Grammars\Grammar';

		switch ($driver)
		{
			case 'sqlite':
				return new Database\Connectors\SQLite;
			break;

			case 'mysql':
				return new Database\Connectors\MySQL;
			break;

			case 'pgsql':
				return new Database\Connectors\Postgres;

			case 'sqlsrv':
				return new Database\Connectors\SQLServer;

			default:
				throw new \Exception("Database driver [$driver] is not supported.");
		}

		return $thing;
	}

}
```

### Tests

Tests are written in PHPUnit. As much as possible a single assertion should occur per test. Tests should be as short and as readable as possible, with neccessary abstraction done in a verbose manner so it is obvious what is happening.

```php
<?php

class SomethingTest extends PHPUnit_Framework_TestCase {

	public function testa_methodDoesSomethingThatYouWantItToDoInCamelCase
	{
		$this->populate_with_two_values();
		$this->assertTrue(true);
	}

	/**
	 * Test basic controller filters are called.
	 */
	public function testAssignedBeforeFiltersAreRun()
	{
		$_SERVER['test-all-after'] = false;
		$_SERVER['test-all-before'] = false;

		Controller::call('filter@index');

		$this->assertTrue($_SERVER['test-all-after']);
		$this->assertTrue($_SERVER['test-all-before']);
	}

}
```
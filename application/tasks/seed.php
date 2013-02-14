<?php

class Seed_Task {

	/**
	 * Run the seed command.
	 * 
	 * @param array  $arguments The arguments sent to the seed command.
	 */
	public function run($arguments = array())
	{
		// Arguments sent to this command are the seeds you want to be run.
		// For example:
		// php artisan seed blah would run the blah seed.
		// Otherwise all seeds in the bundle directory are run.
		if (count($arguments) != 0)
		{
			$seeds = array();

			foreach ($arguments as $argument)
			{
				list($bundle, $seed) = Bundle::parse($argument);

				$file = Bundle::path($bundle).'seeds/'.$seed.EXT;

				$seeds[] = $this->use_seed_file($file, $bundle);
			}
		}
		else
		{
			echo "Attempting to run all seeds...".PHP_EOL;

			$seeds = $this->seeds(null);
		}

		if (count($seeds) == 0)
		{
			echo "No seeds have been created.".PHP_EOL;
			echo "Create seeds with:".PHP_EOL; 
			echo "php artisan seed:make <name of seed>";

			return;
		}

		foreach ($seeds as $seed)
		{
			$seed['task']->run();

			echo 'Seeded: '.$this->display($seed).PHP_EOL;
		}

	}

	/**
	 * Create a new seed file within a bundle (including application).
	 * 
	 * @param  array   $arguments  The arguments sent to this task.
	 * @return string  $file       The seed filename created by this task.
	 */
	public function make($arguments = array())
	{
		if (count($arguments) == 0)
		{
			throw new \Exception("I need to know the name of the seed.".PHP_EOL."Usage: php artisan seed:make <name of new seed file>");
		}

		list($bundle, $seed) = Bundle::parse($arguments[0]);

		$path = Bundle::path($bundle).'seeds'.DS;

		if ( ! is_dir($path)) mkdir($path);

		$file = $path.$seed.EXT;

		// Write the seed to disk using a stub to prepopulate.
		File::put($file, $this->stub($bundle, $seed));

		echo "New seed successfully made in $file";

		// Once the seed has been created, we'll return the
		// seed file name so it can be used by the task
		// consumer if necessary for further work.
		return $file;
	}

	/**
	 * Get the stub seed with the proper class name.
	 *
	 * @param  string  $bundle
	 * @param  string  $seed
	 * @return string  The file as completed with substitutions.
	 */
	protected function stub($bundle, $seed)
	{
		$stub = File::get(Bundle::path($bundle).'tasks/stubs/seed'.EXT);

		$prefix = Bundle::class_prefix($bundle);

		// The class name is formatted similarly to tasks and controllers,
		// where the bundle name is prefixed to the class if it is not in
		// the default "application" bundle.
		$class = $prefix.Str::classify($seed);

		return str_replace('{{class}}', $class, $stub);
	}

	/**
	 * Prepare a seed file to be used.
	 * 
	 * @param string $file    The file name containing the seed.
	 * @param string $bundle  The bundle of the seed.
	 * @return array          An array containing the seed and details of it.
	 */
	public function use_seed_file($file, $bundle)
	{
		// Take the basename of the file and remove the PHP file
		// extension, for display purposes.
		$name = str_replace(EXT, '', basename($file));

		// Check the file exists.
		if (! is_file($file))
		{
			throw new \Exception("Cannot run seed $name. File $file does not exist.");
		}

		require_once $file;

		// Seeds that exist within bundles other than the default
		// will be prefixed with the bundle name to avoid any possible
		// naming collisions with other bundle's seeds.
		$prefix = Bundle::class_prefix($bundle);

		$class = $prefix.\Laravel\Str::classify($name);

		$task = new $class;

		return compact('bundle', 'name', 'task');
	}

	/**
	 * Grab and resolve all of the seeds for a bundle.
	 *
	 * @param  string  $bundle The name of the bundle to seed. If null, all of them.
	 * @return array   Seeds to run - an array including their object, name and bundle.
	 */
	public function seeds($bundle)
	{
		$seeds = array();

		// If no bundle was given to the command, we'll grab every bundle for
		// the application, including the "application" bundle, which is not
		// returned by "all" method on the Bundle class.
		if (is_null($bundle))
		{
			$bundles = array_merge(Bundle::names(), array('application'));
		}
		else
		{
			$bundles = array($bundle);
		}

		foreach ($bundles as $bundle)
		{
			$files = glob(Bundle::path($bundle).'seeds/*'.EXT);

			// When open_basedir is enabled, glob will return false on an
			// empty directory, so we will create an empty array in this
			// case so the application doesn't bomb out.
			if ($files === false)
			{
				$files = array();
			}

			foreach ($files as $file)
			{
				// Prepare seed for action and add to the array.
				$seeds[] = $this->use_seed_file($file, $bundle);
			}
		}

		return $seeds;
	}

	/**
	 * Get the seed bundle and name for display from the seed array passed.
	 *
	 * @param  array   $seed  A seed array.
	 * @return string  The bundle/name of the seed.
	 */
	protected function display($seed)
	{
		return $seed['bundle'].'/'.$seed['name'];
	}

}
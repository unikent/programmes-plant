<?php namespace Filch;

class Cache extends \Laravel\Cache\Drivers\Driver {

	/**
	 * The path to which the cache files should be written.
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * Create a new File cache driver instance.
	 *
	 * @param  string  $path
	 * @return void
	 */
	public function __construct($path)
	{
		$this->path = $path;
	}

	/**
	 * Determine if an item exists in the cache.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function has($key)
	{
		return ( ! is_null($this->get($key)));
	}

	/**
	 * Retrieve an item from the cache driver.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	protected function retrieve($key)
	{
		$key = str_replace('.', DS, $key);

		if ( ! file_exists($this->path.$key)) return null;

		// File based caches store have the expiration timestamp stored in
		// UNIX format prepended to their contents. We'll compare the
		// timestamp to the current time when we read the file.
		if (time() >= substr($cache = file_get_contents($this->path.$key), 0, 10))
		{
			return $this->forget($key);
		}

		return unserialize(substr($cache, 10));
	}

	/**
	 * Write an item to the cache for a given number of minutes.
	 *
	 * <code>
	 *		// Put an item in the cache for 15 minutes
	 *		Cache::put('name', 'Taylor', 15);
	 * </code>
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  int     $minutes
	 * @return void
	 */
	public function put($key, $value, $minutes)
	{
		if ($minutes <= 0) return;

		$key = str_replace('.', DS, $key);
		$subDir = explode(DS, $key);

		if (count($subDir) > 1)
		{
			array_pop($subDir);
			$testPath = $this->path.implode(DS, $subDir);
			if ( ! @is_dir($testPath))
			{
				if ( ! @mkdir($testPath, 0777, true))
				{
					return false;
				}
			}
		}

		$value = $this->expiration($minutes).serialize($value);

		file_put_contents($this->path.$key, $value, LOCK_EX);
	}

	/**
	 * Write an item to the cache for five years.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function forever($key, $value)
	{
		return $this->put($key, $value, 2628000);
	}

	/**
	 * Delete an item from the cache.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function forget($key)
	{
		$key = str_replace('.', DS, $key);
		if (file_exists($this->path.$key)) @unlink($this->path.$key);
	}

	/**
	 * Empty a cache directory
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function purge($key)
	{
		$key = str_replace('.', DS, $key);

		if ( ! is_dir($this->path.$key)) {
			throw new FilchException('Cannot delete a non-existent cache directory!');
		}

		$this->clean($key);
	}

	/**
	 * Empty/Delete a file/directory
	 * @param  string  $key
	 * @param  boolean $rm
	 * @return void
	 */
	protected function clean($key, $rm = false)
	{
		foreach ($open = scandir($this->path.$key) as $item) {
			if ($item != '.' && $item != '..') {
				if (!@is_dir($current = $this->path.$key.DS.$item)) {
					unlink($current);
				} else {
					$this->clean($key.DS.$item, true);
				}
			}
		}

		if($rm and count(scandir($this->path.$key)) <= 2) {
			if ( ! rmdir($this->path.$key)) {
				throw new FilchException('Unable to delete cache directory: '.$key);
			}
		}
	}


}

class FilchException extends \Exception {}

<?php

class Profiler {

	/**
	 * All of the recorded logs
	 *
	 * @var array
	 */
	public static $logs = array();

	/**
	 * The number of messages logged (not including memory, speed, and error
	 * logs)
	 *
	 * @var int
	 */
	public static $logs_count = 0;

	/**
	 * The number of memory logs
	 *
	 * @var int
	 */
	public static $memory_logs = 0;

	/**
	 * The number of speed logs
	 *
	 * @var int
	 */
	public static $speed_logs = 0;

	/**
	 * The number of error logs
	 *
	 * @var int
	 */
	public static $error_logs = 0;

	/**
	 * The number of duplicate queries
	 *
	 * @var int
	 */
	public static $query_duplicates = 0;

	/**
	 * The total time taken to execute all of the queries
	 *
	 * @var int
	 */
	public static $query_total_time = 0;

	/**
	 * All of the executed queries
	 *
	 * @var array
	 */
	public static $queries = array();

	/**
	 * All of the loaded files
	 *
	 * @var array
	 */
	public static $files = array();

	/**
	 * The total size of all the loaded files
	 *
	 * @var string
	 */
	public static $files_total_size = 0;

	/**
	 * The size of the largest loaded file
	 *
	 * @var string
	 */
	public static $files_largest = 0;

	/**
	 * Log a message
	 *
	 * @param  string  $message
	 * @return void
	 */
	public static function log($message)
	{
		static::$logs[] = array(
			'type'    => 'log',
			'message' => $message,
		);

		static::$logs_count++;
	}

	/**
	 * Log the current memory usage or the memory used to store a variable
	 *
	 * @param  string  $name
	 * @param  mixed   $value
	 * @return void
	 */
	public static function log_memory($name = FALSE, $value = FALSE)
	{
		if($name !== FALSE)
		{
			static::$logs[] = array(
				'type'    => 'memory',
				'message' => gettype($value) . ': ' . $name,
				'data'    => static::readable_file_size(strlen(serialize($value))),
			);
		}

		else
		{
			static::$logs[] = array(
				'type'    => 'memory',
				'message' => 'Current memory used',
				'data'    => static::readable_file_size(memory_get_usage()),
			);
		}

		static::$memory_logs++;
	}

	/**
	 * Log the current execution time
	 *
	 * @param  string  $message
	 * @return void
	 */
	public static function log_speed($message = 'Benchmark')
	{
		if($message == 'Benchmark')
		{
			$message .= ' #' . (static::$speed_logs + 1);
		}

		static::$logs[] = array(
			'type'    => 'speed',
			'message' => $message,
			'data'    => static::load_time() * 1000 . ' ms'
		);

		static::$speed_logs++;
	}

	/**
	 * Log an error
	 *
	 * @param  Exception  $exception
	 * @param  string     $message
	 * @return void
	 */
	public static function log_error($exception, $message = 'error')
	{
		$message  = 'Line ' . $exception->getLine() . ' ' . $message . '<br>';
		$message .= $exception->getFile();

		static::$logs[] = array(
			'type'    => 'error', 
			'message' => $message,
		);

		static::$error_logs++;
	}

	/**
	 * Get the current execution time
	 *
	 * @param  int  $decimals
	 * @return int
	 */
	public static function load_time($decimals = 5)
	{
		return number_format(microtime(TRUE) - LARAVEL_START, $decimals);
	}

	/**
	 * Get the highest memory usage
	 *
	 * @return string
	 */
	public static function memory()
	{
		return static::readable_file_size(memory_get_peak_usage());
	}

	/**
	 * Build all of the current profiling data
	 *
	 * @return array
	 */
	public static function compile_data()
	{
		// Get the file data
		$files = get_included_files();

		foreach($files as $file)
		{
			$size = filesize($file);

			static::$files[] = array(
				'path' => $file,
				'size' => static::readable_file_size($size),
			);

			static::$files_total_size += $size;

			if($size > static::$files_largest)
			{
				static::$files_largest = $size;
			}
		}

		// Now that we've gathered all the data, let's do the finishing touches
		static::$files_total_size = static::readable_file_size(static::$files_total_size);
		static::$files_largest    = static::readable_file_size(static::$files_largest);

		return array(
			'logs'               => static::$logs,
			'logs_count'         => static::$logs_count,
			'error_logs'         => static::$error_logs,
			'memory_logs'        => static::$memory_logs,
			'speed_logs'         => static::$speed_logs,
			'load_time'          => static::load_time(),
			'queries'            => static::$queries,
			'query_total_time'   => static::$query_total_time,
			'query_duplicates'   => static::$query_duplicates,
			'memory'             => static::memory(),
			'files'              => static::$files,
			'files_total_size'   => static::$files_total_size,
			'files_largest'      => static::$files_largest,
			'memory_limit'       => ini_get('memory_limit'),
			'max_execution_time' => ini_get('max_execution_time'),
		);
	}

	/**
	 * Convert a file's size into a readable format
	 *
	 * @param  int     $size
	 * @param  string  $format
	 * @return string
	 */
	private static function readable_file_size($size, $format = null)
	{
		// adapted from code at http://aidanlister.com/repos/v/function.size_readable.php
		$sizes = array('bytes', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');

		if(is_null($format))
		{
			$format = '%01.2f %s';
		}

		$lastsizestring = end($sizes);

		foreach ($sizes as $sizestring)
		{
			if ($size < 1024)
			{
				break;
			}

			if ($sizestring != $lastsizestring)
			{
				$size /= 1024;
			}
		}

		// Bytes aren't normally fractional
		if($sizestring == $sizes[0])
		{
			$format = '%01d %s';
		}

		return sprintf($format, $size, $sizestring);
	}
}
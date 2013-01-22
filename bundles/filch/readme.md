# Filch Laravel Bundle

Extend the file cache functionality of Laravel to support directories.

## Installation

1. Add Filch to your bundles.php

		'filch' => ['auto' => true]

2. Use the `filch` as your cache driver in `configs/cache.php`

		'driver' => 'filch'

3. Or use it manually

		$cache = Cache::driver('filch');
		$cache->put('this', 'that', 454545);


## Usage

	Cache::put('latest.posts', $data, 343434);

Creates a directory `latest` and a file `posts`

	Cache::put('latest.funny.posts', $data, 435454);

Creates a directory `latest`, subdirectory `funny` and a file `posts`

	$posts = Cache::sear('blog.posts', $data);

Gets `storage/cache/blog/posts` and puts `$data` if it doesn't find it

	Cache::purge('latest');

Empties the `latest` directory recursively

## Credits

To FuelPHP and Laravel people.
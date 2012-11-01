# Profiler Bundle

## Installation

To install the bundle run the following commands:

`php artisan bundle:install profiler`

`php artisan bundle:publish`

Once that is complete you will need to add the bundle to the **auto** array in the application config file.

## Displaying Profiler

There are several ways you can load Profiler onto your site. For example you can simply use a view:

```php
echo View::make('profiler::display');
```

Of course, you can just nest the view if you want. Or, you can simply use the profiler filter:

```php
public function __construct()
{
	$this->filter('after', 'profiler');
}
```

## Logging

Profiler lets you debug your code easily. You can log a message by doing:

```php
Profiler::log('This is my message!');
```

Want to benchmark your code? Easy!

```php
Profiler::log_speed('Load time to reach this checkpoint');
```

Need to watch your memory usage? Just use the **log_memory** method to see the memory currently used:

```php
Profiler::log_memory('A message to keep track of where I am');
```

You can even keep track of the memory used by a variable:

```php
$somevariable = 'somevalue';

Profiler::log_memory('my variable', $somevariable);
```

Of course, you can also log errors:

```php
Profiler::log_error(new Exception, 'Oops I did a mistake!');
```
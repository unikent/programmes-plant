<?php

Autoloader::map(array(
	'Profiler' => __DIR__ . DS . 'libraries' . DS . 'profiler.php',
));

Event::listen('laravel.query', function($sql, $bindings, $time)
{
	if(in_array($sql, Profiler::$queries))
	{
		Profiler::$query_duplicates++;
	}

	Profiler::$query_total_time += (double)$time;
	Profiler::$queries[] = $sql;
});

View::composer('profiler::display', function ($profiler)
{
	foreach(Profiler::compile_data() as $key => $value)
	{
		$profiler->with($key, $value);
	}
});

Filter::register('profiler', function()
{
	echo View::make('profiler::display');
});
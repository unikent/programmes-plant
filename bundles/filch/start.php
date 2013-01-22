<?php

Autoloader::map(array(
	'Filch\\Cache' => __DIR__.DS.'cache'.EXT,
));

Cache::extend('filch', function(){
	return new Filch\Cache(path('storage').'cache'.DS);
});
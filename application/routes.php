<?php


//Index
Route::any('([0-9]{4})/(ug|pg)', 'dash@index');

//user managment
Route::get('([0-9]{4})/(ug|pg)/roles', 'roles@index');
Route::get('([0-9]{4})/(ug|pg)/help', 'help@index');

//Login/out
Route::any('login', 'dash@login');
Route::any('logout', 'dash@logout');

//Do Globals
Route::any('([0-9]{4})/(ug|pg)/globals', 'globals@index');
Route::any('([0-9]{4})/(ug|pg)/globals/(:any?)/(:num?)', 'globals@(:3)');
Route::get('([0-9]{4})/(ug|pg)/globals/(:num)/(:any)/(:num)', 'globals@(:4)');

//Do Programmes
Route::any('([0-9]{4})/(ug|pg)/programmes', 'programmes@index');
Route::any('([0-9]{4})/(ug|pg)/programmes/(:any?)/(:num?)', 'programmes@(:3)');
Route::get('([0-9]{4})/(ug|pg)/programmes/(:num)/(:any)/(:num)', 'programmes@(:4)');

//Access Meta systems
Route::any('([0-9]{4})/(ug|pg)/meta/programmes', 'programmes_meta@index');
Route::any('([0-9]{4})/(ug|pg)/meta/programmes/(:any?)/(:num?)', 'programmes_meta@(:3)');

Route::any('([0-9]{4})/(ug|pg)/meta/globals', 'globals_meta@index');
Route::any('([0-9]{4})/(ug|pg)/meta/globals/(:any?)/(:num?)', 'globals_meta@(:3)');


// Customised routing for campuses
Route::any('([0-9]{4})/(ug|pg)/campuses', 'campuses@index');
Route::any('([0-9]{4})/(ug|pg)/campuses/(:any?)/(:num?)', 'campuses@(:3)');

// Customised routing for schools
Route::any('([0-9]{4})/(ug|pg)/schools', 'schools@index');
Route::any('([0-9]{4})/(ug|pg)/schools/(:any?)/(:num?)', 'schools@(:3)');

// Customised routing for faculties
Route::any('([0-9]{4})/(ug|pg)/faculties', 'faculties@index');
Route::any('([0-9]{4})/(ug|pg)/faculties/(:any?)/(:num?)', 'faculties@(:3)');

// Customised routing for awards
Route::any('([0-9]{4})/(ug|pg)/awards', 'awards@index');
Route::any('([0-9]{4})/(ug|pg)/awards/(:any?)/(:num?)', 'awards@(:3)');

// Customised routing for leaflets
Route::any('([0-9]{4})/(ug|pg)/leaflets', 'leaflets@index');
Route::any('([0-9]{4})/(ug|pg)/leaflets/(:any?)/(:num?)', 'leaflets@(:3)');

//Any page without a root goes to index
Route::any('/',function(){
       return Redirect::to(date('Y').'/ug');   
});

/*
|--------------------------------------------------------------------------
| Application 404 & 500 Error Handlers
|--------------------------------------------------------------------------
|
| To centralize and simplify 404 handling, Laravel uses an awesome event
| system to retrieve the response. Feel free to modify this function to
| your tastes and the needs of your application.
|
| Similarly, we use an event to handle the display of 500 level errors
| within the application. These errors are fired when there is an
| uncaught exception thrown in the application.
|
*/

Event::listen('404', function()
{
	return Response::error('404');
});

Event::listen('500', function()
{
	return Response::error('500');
});

/*
|--------------------------------------------------------------------------
| Route Filters
|--------------------------------------------------------------------------
|
| Filters provide a convenient method for attaching functionality to your
| routes. The built-in "before" and "after" filters are called before and
| after every request to your application, and you may even create other
| filters that can be attached to individual routes.
|
| Let's walk through an example...
|
| First, define a filter:
|
|		Route::filter('filter', function()
|		{
|			return 'Filtered!';
|		});
|
| Next, attach the filter to a route:
|
|		Router::register('GET /', array('before' => 'filter', function()
|		{
|			return 'Hello World!';
|		}));
|
*/

Route::filter('before', function()
{
	// Do stuff before every request to your application...
});

Route::filter('after', function($response)
{
	// Do stuff after every request to your application...
});

Route::filter('csrf', function()
{
	if (Request::forged()) return Response::error('500');
});

Route::filter('auth', function()
{
    Session::put('referrer', URL::current());
	if (Auth::guest()) return Redirect::to('login');
});
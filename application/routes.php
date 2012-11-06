<?php

/*
 * Anything admin facing requires authorisation.
 */
Route::group(array('before' => 'auth'), function(){
	// Any page without a root goes to index
	Route::any('/',function(){
	       return Redirect::to(date('Y').'/ug');   
	});

	// Index
	Route::any('([0-9]{4})/(ug|pg)', 'dash@index');

	// Roles managment
	Route::get('([0-9]{4})/(ug|pg)/roles', 'roles@index');

	// Automatic routing of RESTful controller
	Route::controller('roles');

	// Help page
	Route::get('([0-9]{4})/(ug|pg)/help', 'help@index');

	// Do global settings
	Route::any('([0-9]{4})/(ug|pg)/globalsettings', 'globalsettings@index');
	Route::any('([0-9]{4})/(ug|pg)/globalsettings/(:any?)/(:num?)', 'globalsettings@(:3)');
	Route::get('([0-9]{4})/(ug|pg)/globalsettings/(:num)/(:any)/(:num)', 'globalsettings@(:4)');

	// Do Programmes
	Route::any('([0-9]{4})/(ug|pg)/programmes', 'programmes@index');
	Route::any('([0-9]{4})/(ug|pg)/programmes/(:any?)/(:num?)', 'programmes@(:3)');
	Route::get('([0-9]{4})/(ug|pg)/programmes/(:num)/(:any)/(:num)', 'programmes@(:4)');

	// Access Meta systems
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
});

// Login/out
Route::any('login', 'auth@login');
Route::any('logout', 'auth@logout');

Event::listen('404', function()
{
	return Response::error('404');
});

Event::listen('500', function()
{
	return Response::error('500');
});

Route::filter('csrf', function()
{
	if (Request::forged()) return Response::error('500');
});

Route::filter('auth', function()
{
    Session::put('referrer', URL::current());

    if (Auth::guest()) {
    	return Redirect::to('login');
    }
});
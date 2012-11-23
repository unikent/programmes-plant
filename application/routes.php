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
	Route::any('([0-9]{4})/(ug|pg)', 'programmes@index');

	// Roles managment
	Route::get('([0-9]{4})/(ug|pg)/roles', 'roles@index');

	// Automatic routing of RESTful controller
	Route::controller('roles');

	// Do global settings
	Route::any('([0-9]{4})/(ug|pg)/globalsettings', 'globalsettings@index');
	Route::any('([0-9]{4})/(ug|pg)/globalsettings/(:any)', 'globalsettings@(:3)');
	Route::any('([0-9]{4})/(ug|pg)/globalsettings/(:any)/(:num)', 'globalsettings@(:3)');

	// Do global settings
	Route::any('([0-9]{4})/(ug|pg)/programmesettings', 'programmesettings@index');
	Route::any('([0-9]{4})/(ug|pg)/programmesettings/(:any)', 'programmesettings@(:3)');
	Route::any('([0-9]{4})/(ug|pg)/programmesettings/(:any)/(:num)', 'programmesettings@(:3)');

	// Do Programmes
	Route::any('([0-9]{4})/(ug|pg)/programmes', 'programmes@index');
	Route::any('([0-9]{4})/(ug|pg)/programmes/(:any?)/(:num?)', 'programmes@(:3)');
	Route::get('([0-9]{4})/(ug|pg)/programmes/(:num)/(:any)/(:num)', 'programmes@(:4)');

	// Access fields systems
	Route::any('fields/programmes', 'programmefields@index');
	Route::post('fields/programmes/reorder', 'programmefields@reorder');
	Route::any('fields/programmes/(:any?)', 'programmefields@(:1)');
	Route::any('fields/programmes/(:any?)/(:num?)', 'programmefields@(:1)');

	Route::any('fields/globalsettings', 'globalsettingfields@index');
	Route::any('fields/globalsettings/(:any?)', 'globalsettingfields@(:1)');
	Route::any('fields/globalsettings/(:any?)/(:num?)', 'globalsettingfields@(:1)');

	Route::any('fields/programmesettings', 'programmesettingfields@index');
	Route::any('fields/programmesettings/(:any?)', 'programmesettingfields@(:1)');
	Route::any('fields/programmesettings/(:any?)/(:num?)', 'programmesettingfields@(:1)');

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
	
	// Customised routing for sections
	Route::any('sections', 'programmesections@index');
	Route::post('sections/reorder', 'programmesections@reorder');
	Route::any('sections/(:any?)/(:num?)', 'programmesections@(:1)');

	// Customised routing for leaflets
	Route::any('([0-9]{4})/(ug|pg)/leaflets', 'leaflets@index');
	Route::any('([0-9]{4})/(ug|pg)/leaflets/(:any?)/(:num?)', 'leaflets@(:3)');

	// Customised routing for subjects
	Route::any('([0-9]{4})/(ug|pg)/subjects', 'subjects@index');
	Route::any('([0-9]{4})/(ug|pg)/subjects/(:any?)/(:num?)', 'subjects@(:3)');
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
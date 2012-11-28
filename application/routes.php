<?php

/*
 * Anything admin facing requires authorisation.
 */

if (Request::env() == 'test')
{
	$before = '';
}
else {
	$before = 'auth';
}

Route::group(array('before' => ''), function(){
	// Any page without a root goes to index
	Route::any('/',function(){
	       return Redirect::to(date('Y').'/ug/');   
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
	Route::any('(ug|pg)/fields/programmes', 'programmefields@index');
	Route::post('(ug|pg)/fields/programmes/reorder', 'programmefields@reorder');
	Route::any('(ug|pg)/fields/programmes/(:any?)', 'programmefields@(:2)');
	Route::any('(ug|pg)/fields/programmes/(:any?)/(:num?)', 'programmefields@(:2)');

	Route::any('(ug|pg)/fields/globalsettings', 'globalsettingfields@index');
	Route::any('(ug|pg)/fields/globalsettings/(:any?)', 'globalsettingfields@(:2)');
	Route::any('(ug|pg)/fields/globalsettings/(:any?)/(:num?)', 'globalsettingfields@(:2)');

	Route::any('(ug|pg)/fields/programmesettings', 'programmesettingfields@index');
	Route::any('(ug|pg)/fields/programmesettings/(:any?)', 'programmesettingfields@(:2)');
	Route::any('(ug|pg)/fields/programmesettings/(:any?)/(:num?)', 'programmesettingfields@(:2)');

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
	Route::any('(ug|pg)/sections', 'programmesections@index');
	Route::post('(ug|pg)/sections/reorder', 'programmesections@reorder');
	Route::any('(ug|pg)/sections/(:any?)/(:num?)', 'programmesections@(:2)');

	// Customised routing for leaflets
	Route::any('([0-9]{4})/(ug|pg)/leaflets', 'leaflets@index');
	Route::any('([0-9]{4})/(ug|pg)/leaflets/(:any?)/(:num?)', 'leaflets@(:3)');

	// Customised routing for subjects
	Route::any('([0-9]{4})/(ug|pg)/subjects', 'subjects@index');
	Route::any('([0-9]{4})/(ug|pg)/subjects/(:any?)/(:num?)', 'subjects@(:3)');

	// Customised routing for subject categories
	Route::any('([0-9]{4})/(ug|pg)/subjectcategories', 'subjectcategories@index');
	Route::any('([0-9]{4})/(ug|pg)/subjectcategories/(:any?)/(:num?)', 'subjectcategories@(:3)');
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
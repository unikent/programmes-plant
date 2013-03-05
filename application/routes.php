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
	       return Redirect::to('2014/ug/');
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
	Route::any('([0-9]{4})/(ug|pg)/globalsettings/(:num?)/(:any?)/(:num?)', 'globalsettings@(:4)');

	// Do global settings
	Route::any('([0-9]{4})/(ug|pg)/programmesettings', 'programmesettings@index');
	Route::any('([0-9]{4})/(ug|pg)/programmesettings/(:any)', 'programmesettings@(:3)');
	Route::any('([0-9]{4})/(ug|pg)/programmesettings/(:any)/(:num)', 'programmesettings@(:3)');
	Route::get('([0-9]{4})/(ug|pg)/programmesettings/(:num)/(:any)/(:num)', 'programmesettings@(:4)');

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


	Route::any('users', 'users@index');
	Route::any('users/(add|edit|delete)', 'users@(:1)');


	// API

	// Routing for undergraduate API, the only API currently supported.
	Route::any(array(
			'/api/([0-9]{4})/undergraduate',
			'/api/([0-9]{4})/undergraduate/programmes', 
			'/api/([0-9]{4})/undergraduate/programmes.(json|xml)'
	), 'api@index');

	Route::get(array('/api/([0-9]{4})/undergraduate/programmes/(:num?)','/api/([0-9]{4})/undergraduate/programmes/(:num?).(json|xml)'), 'api@programme');
	Route::any(array('/api/([0-9]{4})/undergraduate/subjects','/api/([0-9]{4})/undergraduate/subjects.(json|xml)'), 'api@subject_index');
	Route::get(array('/api/(:any).(json|xml)','/api/(:any)'), 'api@data');

	Route::any('/api/preview/(:any?)', 'api@preview');


	// XCRI-CAP Feeds
	Route::any('/xcri-cap/(undergraduate)/([0-9]{4})', 'xcri-cap@index');
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

Route::filter('before', function()
{
	// Push IE to max avaliable.
	header('X-UA-Compatible: IE=Edge,chrome=1');
});

Route::filter('auth', function($permissions)
{
    Session::put('referrer', URL::current());

    // Check user is logged in
    if (Auth::guest()) 
    {
    	return Redirect::to('login');
    }

	// If there are permissions, check user has them
	if (sizeof($permissions) !== 0 && !Auth::user()->can($permissions))
	{
		//User is not allowed here. Tell them
		$page = View::make('admin.inc.no_permissions', $permissions);
		return View::make('layouts.admin', array('content'=> $page));
	}

	// All okay?
});
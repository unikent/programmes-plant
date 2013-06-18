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
	Route::any('([0-9]{4})/(ug|pg)/programmesettings', 'programmesettings@index');
	Route::any('([0-9]{4})/(ug|pg)/programmesettings/(:any)', 'programmesettings@(:3)');
	Route::any('([0-9]{4})/(ug|pg)/programmesettings/(:any)/(:num)', 'programmesettings@(:3)');
	Route::get('([0-9]{4})/(ug|pg)/programmesettings/(:num)/(:any)/(:num)', 'programmesettings@(:4)');

	// Do Programmes
	Route::any('([0-9]{4})/(ug|pg)/programmes', 'programmes@index');
	Route::any('([0-9]{4})/(ug|pg)/programmes/(:any?)/(:num?)', 'programmes@(:3)');
	Route::get('([0-9]{4})/(ug|pg)/programmes/(:num)/(:any)/(:num)', 'programmes@(:4)');

	// Access fields systems
	Route::any('(ug|pg)/fields/standard', 'programmefields@index');
	Route::any('(ug|pg)/fields/standard/(:any?)', 'programmefields@(:2)');
	Route::any('(ug|pg)/fields/standard/(:any?)/(:num?)', 'programmefields@(:2)');
	Route::post('(ug|pg)/fields/programmes/reorder', 'programmefields@reorder');
	// Customised routing for immutable fields
	Route::any('fields/immutable', 'globalsettingfields@index');
	Route::any('fields/immutable/(:any?)/(:num?)', 'globalsettingfields@(:1)');
	
	// Customised routing for sections
	Route::any('(ug|pg)/sections', 'programmesections@index');
	Route::post('(ug|pg)/sections/reorder', 'programmesections@reorder');
	Route::any('(ug|pg)/sections/(:any?)/(:num?)', 'programmesections@(:2)');

	// Do global settings
	Route::any('([0-9]{4})/globalsettings', 'globalsettings@index');
	Route::any('([0-9]{4})/globalsettings/(:any)', 'globalsettings@(:2)');
	Route::any('([0-9]{4})/globalsettings/(:any)/(:num)', 'globalsettings@(:2)');
	Route::any('([0-9]{4})/globalsettings/(:num?)/(:any?)/(:num?)', 'globalsettings@(:3)');

	// System settings
	Route::any('settings', 'settings@index');

	// Customised routing for campuses
	Route::any('campuses', 'campuses@index');
	Route::any('campuses/(:any?)/(:num?)', 'campuses@(:1)');

	// Customised routing for schools
	Route::any('schools', 'schools@index');
	Route::any('schools/(:any?)/(:num?)', 'schools@(:1)');

	// Customised routing for faculties
	Route::any('faculties', 'faculties@index');
	Route::any('faculties/(:any?)/(:num?)', 'faculties@(:1)');

	// Customised routing for awards
	Route::any('awards', 'awards@index');
	Route::any('awards/(:any?)/(:num?)', 'awards@(:1)');

	// Customised routing for leaflets
	Route::any('leaflets', 'leaflets@index');
	Route::any('leaflets/(:any?)/(:num?)', 'leaflets@(:1)');

	// Customised routing for subjects
	Route::any('subjects', 'subjects@index');
	Route::any('subjects/(:any?)/(:num?)', 'subjects@(:1)');

	// Customised routing for subject categories
	Route::any('subjectcategories', 'subjectcategories@index');
	Route::any('subjectcategories/(:any?)/(:num?)', 'subjectcategories@(:1)');

	// Users system
	Route::any('users', 'users@index');
	Route::any('users/(add|edit|delete)/(:num?)', 'users@(:1)');



	// Editing suite
	Route::controller('editor');

	// API

	// Routing for undergraduate API
	Route::any(array(
			'/api/([0-9]{4})/undergraduate',
			'/api/([0-9]{4})/undergraduate/programmes', 
			'/api/([0-9]{4})/undergraduate/programmes.(json|xml)'
	), 'api@index');

	Route::get(array('/api/([0-9]{4})/undergraduate/programmes/(:num?)','/api/([0-9]{4})/undergraduate/programmes/(:num?).(json|xml)'), 'api@programme');
	Route::any(array('/api/([0-9]{4})/undergraduate/subjects','/api/([0-9]{4})/undergraduate/subjects.(json|xml)'), 'api@subject_index');
	Route::get(array('/api/(:any).(json|xml)','/api/(:any)'), 'api@data');
	
	// Routing for postgraduate API
	Route::any(array(
			'/api/([0-9]{4})/postgraduate',
			'/api/([0-9]{4})/postgraduate/programmes', 
			'/api/([0-9]{4})/postgraduate/programmes.(json|xml)'
	), 'api@index');

	Route::get(array('/api/([0-9]{4})/postgraduate/programmes/(:num?)','/api/([0-9]{4})/postgraduate/programmes/(:num?).(json|xml)'), 'api@programme');
	Route::any(array('/api/([0-9]{4})/postgraduate/subjects','/api/([0-9]{4})/postgraduate/subjects.(json|xml)'), 'api@subject_index');
	Route::get(array('/api/(:any).(json|xml)','/api/(:any)'), 'api@data');

	Route::any('/api/preview/(:any?)', 'api@preview');
	
	// XCRI-CAP Feed
	Route::any('/api/([0-9]{4})/(undergraduate|postgraduate)/xcri-cap', 'api@xcri_cap');
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
		$page = View::make('admin.inc.no_permissions', array("perms" => $permissions));

		return View::make('layouts.admin', array('content'=> $page));
	}

	// All okay?
});
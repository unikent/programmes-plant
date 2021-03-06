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


	//Notes
	Route::post('notes/create','notes@create');
	Route::post('notes/update','notes@update');

	// index
	Route::any('/', 'programmes@index');
	Route::any('([0-9]{4})', 'programmes@index');

	// programme list
	Route::any('([0-9]{4})/(ug|pg)', 'programmes@list');

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
	Route::any('([0-9]{4})/(ug|pg)/programmes', 'programmes@list');
	Route::any('([0-9]{4})/(ug|pg)/programmes/(:any?)/(:num?)', 'programmes@(:3)');
	Route::get('([0-9]{4})/(ug|pg)/programmes/(:num)/(:any)/(:num)', 'programmes@(:4)');
	Route::get('([0-9]{4})/(ug|pg)/programmes/deliveries/(:num)', 'programmes@deliveries');
	Route::post('([0-9]{4})/(ug|pg)/programmes/(:num)/submit_programme_for_editing/(:num)', 'programmes@submit_programme_for_editing');

	// Customised routing for student profiles
	Route::any('(ug|pg)/profile', 'profiles@index');
	Route::any('(ug|pg)/profile/(:any?)/(:num?)', 'profiles@(:2)');
	Route::get('(ug|pg)/profile/export', 'profiles@index');

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

	// Customised routing for research staff
	Route::any('staff', 'staff@index');
	Route::any('staff/(:any?)/(:num?)', 'staff@(:1)');
	
	// Customised routing for campuses
	Route::any('campuses', 'campuses@index');
	Route::any('campuses/(:any?)/(:num?)', 'campuses@(:1)');

	// Customised routing for schools
	Route::any('schools', 'schools@index');
	Route::any('schools/(:any?)/(:num?)', 'schools@(:1)');

	// Customised routing for faculties
	Route::any('faculties', 'faculties@index');
	Route::any('faculties/(:any?)/(:num?)', 'faculties@(:1)');

	// Customised routing for campuses
	Route::any('images', 'images@index');
	Route::post('images/upload','images@upload');
	Route::any('images/(:any?)/(:num?)', 'images@(:1)');

	// Customised routing for awards
	Route::any('(ug|pg)/awards', 'awards@index');
	Route::any('(ug|pg)/awards/(:any?)/(:num?)', 'awards@(:2)');

	// Customised routing for leaflets
	Route::any('(ug|pg)/leaflets', 'leaflets@index');
	Route::any('(ug|pg)/leaflets/(:any?)/(:num?)', 'leaflets@(:2)');

	// Customised routing for subjects
	Route::any('(ug|pg)/subjects', 'subjects@index');
	Route::any('(ug|pg)/subjects/(:any?)/(:num?)', 'subjects@(:2)');

	// Customised routing for subject categories
	Route::any('(ug|pg)/subjectcategories', 'subjectcategories@index');
	Route::any('(ug|pg)/subjectcategories/(:any?)/(:num?)', 'subjectcategories@(:2)');

	// Users system
	Route::any('users', 'users@index');
	Route::any('users/(add|edit|delete)/(:num?)', 'users@(:1)');



	// Editing suite
	Route::controller('editor');

	// API

	// Routing for API index
	Route::any(array(
		'/api/([0-9]{4}|current)/(undergraduate|postgraduate|all)',
		'/api/([0-9]{4}|current)/(undergraduate|postgraduate|all)/programmes.(json|xml|csv)',
		'/api/([0-9]{4}|current)/(undergraduate|postgraduate|all)/programmes'
	), 'api@index');



	Route::get(array(
		'/api/([0-9]{4}|current)/(undergraduate|postgraduate|all)/hear.(json|xml|csv)',
		'/api/([0-9]{4}|current)/(undergraduate|postgraduate|all)/hear'
	), 'api@hear');

	Route::get(array(
		'/api/([0-9]{4}|current)/(undergraduate|postgraduate|all)/hear2.(json|xml|csv)',
		'/api/([0-9]{4}|current)/(undergraduate|postgraduate|all)/hear2'
	), 'api@hear2');

	// Routing for API fees
	Route::any(array(
		'/api/([0-9]{4}|current|preview)/(undergraduate|postgraduate)/fees.(json|xml|csv)',
		'/api/([0-9]{4}|current|preview)/(undergraduate|postgraduate)/fees'
	), 'api@fees_index');

	Route::any(array(
		'/api/([0-9]{4}|current)/(undergraduate|postgraduate)/fees/([0-9]{4}).(json|xml|csv)',
		'/api/([0-9]{4}|current)/(undergraduate|postgraduate)/fees/([0-9]{4})'
	), 'api@fees_index_for_year');



	Route::get(array(
		'/api/([0-9]{4}|current)/(undergraduate|postgraduate)/programmes/(:num).(json|xml|csv)',
		'/api/([0-9]{4}|current)/(undergraduate|postgraduate)/programmes/(:num)'
	), 'api@programme');
	Route::any(array(
		'/api/([0-9]{4})/(undergraduate|postgraduate)/subjects.(json|xml|csv)',
		'/api/([0-9]{4}|current)/(undergraduate|postgraduate)/subjects'
	), 'api@subject_index');

	Route::get('/api/(undergraduate|postgraduate)/year', 'api@years');


	Route::get('/api/profiles', 'api@allProfiles');
	Route::get('/api/(undergraduate|postgraduate)/profiles', 'api@profiles');
	Route::get(array(
		'/api/(undergraduate|postgraduate)/profile/(:any).(json|xml|csv)',
		'/api/(undergraduate|postgraduate)/profile/(:any)'
   ), 'api@profile');


	Route::get(array('/api/(undergraduate|postgraduate)/(:any)/(:num).(json|xml|csv)', '/api/(undergraduate|postgraduate)/(:any)/(:num)'), 'api@data_single_for_level');
	Route::get(array('/api/(undergraduate|postgraduate)/(:any).(json|xml|csv)', '/api/(undergraduate|postgraduate)/(:any)'), 'api@data_for_level');



	Route::get(array('/api/(:any)/(:num).(json|xml)', '/api/(:any)/(:num)'), 'api@data_single');

	Route::get(array('/api/(:any).(json|xml)', '/api/(:any)'), 'api@data');




	Route::any('/api/preview/(undergraduate|postgraduate)/(:any?)', 'api@preview');

	Route::any('/api/simpleview/(undergraduate|postgraduate)/(:any?)', 'api@simpleview');

	// XCRI-CAP Feed
	Route::any('/api/xcri-cap', 'api@xcri_cap');
	Route::any('/api/([0-9]{4}|current)/xcri-cap', 'api@xcri_cap');

	// bare-bones programme list
	Route::get('/api/([0-9]{4}|current)/(undergraduate|postgraduate)/courses', 'api@simplelist');

	Route::get('/api/([0-9]{4}|current)/(undergraduate|postgraduate)/all_courses', 'api@fullsimplelist');



	Route::get('/command/modulerefresh/([0-9]{4}|current)/(undergraduate|postgraduate)/(:num)','api@refresh_modules');
	// Exports
	Route::get('/export/([0-9]{4}|current)/(undergraduate|postgraduate)/courses', 'api@simplelist'); // Duplicate of above, but in export url space
	Route::get('/export/([0-9]{4}|current)/(undergraduate|postgraduate)/course-ids', 'api@verysimplelist'); // Duplicate of above, but really simple output showing just course names and ids
	Route::get('/export/([0-9]{4}|current)/(undergraduate|postgraduate)/kis', 'api@export_kisdata');
	Route::get('/export/([0-9]{4}|current)/(undergraduate|postgraduate)/entry', 'api@export_entrydata');
	Route::get('/export/([0-9]{4}|current)/(undergraduate|postgraduate)/print-courses/(:num?)', 'api@printlist'); // as per the simplelist csv output, but with fields that are more relevant for the printed prospectus
	Route::get('/export/([0-9]{4}|current)/(undergraduate|postgraduate)/last-updated', 'api@export_lastupdated'); 
	Route::get('/export/([0-9]{4}|current)/(undergraduate|postgraduate)/courses-without-fees', 'api@courses_without_fees'); // get courses that don't yet have fees data

	Route::get('/export/([0-9]{4}|current)/(undergraduate|postgraduate)/pos-to-mcr', 'api@export_pos_to_mcr'); // get POS code to MCR list

    Route::get('/export/([0-9]{4}|current)/(undergraduate|postgraduate)/pos-codes', 'api@export_poscodes'); // get pos code list per programme
    Route::get('/export/([0-9]{4}|current)/(undergraduate|postgraduate)/(taught|research|taught-research)/pos-codes', 'api@export_poscodes'); // get pos code list per programme

	Route::get('/export/([0-9]{4}|current)/(undergraduate|postgraduate)/all-fields', 'api@export_allfields'); // get all fields for all courses
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
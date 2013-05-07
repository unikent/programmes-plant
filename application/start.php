<?php

/*
|--------------------------------------------------------------------------
| PHP Display Errors Configuration
|--------------------------------------------------------------------------
|
| Since Laravel intercepts and displays all errors with a detailed stack
| trace, we can turn off the display_errors ini directive. However, you
| may want to enable this option if you ever run into a dreaded white
| screen of death, as it can provide some clues.
|
*/

error_reporting(E_ALL);
//ini_set('display_errors',0);
/*
|--------------------------------------------------------------------------
| Laravel Configuration Loader
|--------------------------------------------------------------------------
|
| The Laravel configuration loader is responsible for returning an array
| of configuration options for a given bundle and file. By default, we
| use the files provided with Laravel; however, you are free to use
| your own storage mechanism for configuration arrays.
|
*/

Laravel\Event::listen(Laravel\Config::loader, function($bundle, $file)
{
	return Laravel\Config::file($bundle, $file);
});

/*
|--------------------------------------------------------------------------
| Register Class Aliases
|--------------------------------------------------------------------------
|
| Aliases allow you to use classes without always specifying their fully
| namespaced path. This is convenient for working with any library that
| makes a heavy use of namespace for class organization. Here we will
| simply register the configured class aliases.
|
*/

$aliases = Laravel\Config::get('application.aliases');

Laravel\Autoloader::$aliases = $aliases;

/*
|--------------------------------------------------------------------------
| Auto-Loader Mappings
|--------------------------------------------------------------------------
|
| Registering a mapping couldn't be easier. Just pass an array of class
| to path maps into the "map" function of Autoloader. Then, when you
| want to use that class, just use it. It's simple!
|
*/

Autoloader::map(array(
	'Revisionable_Controller' => path('app').'controllers/revisionable.php',
	'Fields_Controller' => path('app').'controllers/fields.php',
	'Admin_Controller' => path('app').'controllers/admin.php',
	'Simple_Admin_Controller' => path('app').'controllers/simpleadmin.php',
	'Base_Controller' => path('app').'controllers/base.php',
	'WideImage' => path('app').'libraries/wideimage/WideImage.php',
));

/*
|--------------------------------------------------------------------------
| Auto-Loader Directories
|--------------------------------------------------------------------------
|
| The Laravel auto-loader can search directories for files using the PSR-0
| naming convention. This convention basically organizes classes by using
| the class namespace to indicate the directory structure.
|
*/

Autoloader::directories(array(
	path('app').'models',
	path('app').'libraries',
));

include(path('app').'libraries/macros.php');

/*
|--------------------------------------------------------------------------
| Laravel View Loader
|--------------------------------------------------------------------------
|
| The Laravel view loader is responsible for returning the full file path
| for the given bundle and view. Of course, a default implementation is
| provided to load views according to typical Laravel conventions but
| you may change this to customize how your views are organized.
|
*/

Event::listen(View::loader, function($bundle, $view)
{
	return View::file($bundle, $view, Bundle::path($bundle).'views');
});

/*
|--------------------------------------------------------------------------
| Laravel Language Loader
|--------------------------------------------------------------------------
|
| The Laravel language loader is responsible for returning the array of
| language lines for a given bundle, language, and "file". A default
| implementation has been provided which uses the default language
| directories included with Laravel.
|
*/

Event::listen(Lang::loader, function($bundle, $language, $file)
{
	return Lang::file($bundle, $language, $file);
});

/*
|--------------------------------------------------------------------------
| Enable The Blade View Engine
|--------------------------------------------------------------------------
|
| The Blade view engine provides a clean, beautiful templating language
| for your application, including syntax for echoing data and all of
| the typical PHP control structures. We'll simply enable it here.
|
*/

if (Config::get('application.profiler'))
{
    Profiler::attach();
}

// cut out blade, because we don't need it
Blade::sharpen();

/*
|--------------------------------------------------------------------------
| Set The Default Timezone
|--------------------------------------------------------------------------
|
| We need to set the default timezone for the application. This controls
| the timezone that will be used by any of the date methods and classes
| utilized by Laravel or your application. The timezone may be set in
| your application configuration file.
|
*/

date_default_timezone_set(Config::get('application.timezone'));

/*
|--------------------------------------------------------------------------
| Start / Load The User Session
|--------------------------------------------------------------------------
|
| Sessions allow the web, which is stateless, to simulate state. In other
| words, sessions allow you to store information about the current user
| and state of your application. Here we'll just fire up the session
| if a session driver has been configured.
|
*/

// Setup auto loaders for required bundles (needed for tasks/tests)
Autoloader::namespaces(array(
	'Verify\Models'	=> Bundle::path('verify_ldap') . '../verify/models'
));

Autoloader::map(array(
	// Sessions
	'PhpSession' => Bundle::path('phpsession').'phpsession.php',
	// Cacging
	'Filch\Cache' => Bundle::path('filch').'cache.php',
	// Auth
	'Verify_LDAP' 	=> Bundle::path('verify_ldap') . 'libraries/verify_ldap.php',
	'LDAPConnect' => Bundle::path('verify_ldap').'libraries/ldapconnect.php',
	'Verify' 	=> Bundle::path('verify_ldap') . '../verify/libraries/verify.php',
	'User' => Bundle::path('verify_ldap').'../verify/models/user.php',
	'Permission' => Bundle::path('verify_ldap').'../verify/models/permission.php',
));
// Attach drivers
Session::extend('phpsession', function(){ 	return new PhpSession; 		});
Cache::extend('filch', function(){ 			return new Filch\Cache(path('storage').'cache'.DS); });
Auth::extend('verify_ldap', function() { 	return new Verify_LDAP; 	});

// the API does not need a session. This is also a way to get rid of the no-cache headers being sent
if(strpos(Request::uri(), 'api') === 0)
{
	Config::set('session.driver', '');
}

if (Config::get('session.driver') !== '')
{
	Session::load();
}
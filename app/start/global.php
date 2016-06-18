<?php

/*
|--------------------------------------------------------------------------
| Register The Laravel Class Loader
|--------------------------------------------------------------------------
|
| In addition to using Composer, you may use the Laravel class loader to
| load your controllers and models. This is useful for keeping all of
| your classes in the "global" namespace without Composer updating.
|
*/

ClassLoader::addDirectories(array(

	app_path().'/commands',
	app_path().'/controllers',
	app_path().'/models',
	app_path().'/database/seeds',
	app_path().'/lib',

));

/*
|--------------------------------------------------------------------------
| Application Error Logger
|--------------------------------------------------------------------------
|
| Here we will configure the error logger setup for the application which
| is built on top of the wonderful Monolog library. By default we will
| build a rotating log file setup which creates a new file each day.
|
*/

$logFile = 'log-'.php_sapi_name().'.txt';

Log::useDailyFiles(storage_path().'/logs/'.$logFile);

/*
|--------------------------------------------------------------------------
| Application Error Handler
|--------------------------------------------------------------------------
|
| Here you may handle any errors that occur in your application, including
| logging them or displaying custom views for specific errors. You may
| even register several error handlers to handle different types of
| exceptions. If nothing is returned, the default error view is
| shown, which includes a detailed stack trace during debug.
|
*/
App::error(function(Exception $exception, $code)
{
	if (strpos($exception->getFile(), 'Hybrid') > -1 && ! Request::is(Str::slug(trans('main.login')))) {
		return Redirect::to(Str::slug(trans('main.login')));
	}

	Log::error(Request::url(). '     -     '.$exception);
});


/*
|--------------------------------------------------------------------------
| Maintenance Mode Handler
|--------------------------------------------------------------------------
|
| The "down" Artisan command gives you the ability to put an application
| into maintenance mode. Here, you will define what is displayed back
| to the user if maintenace mode is in effect for this application.
|
*/

App::down(function()
{
	return Response::make("Be right back!", 503);
});

/*
|--------------------------------------------------------------------------
| Bind 404 view
|--------------------------------------------------------------------------
|
|Returns a custom 404 view whenever 404 exceptions is thrown by laravel.
|
*/

App::missing(function($exception)
{
    return Response::make(View::make('Main.404'), 404);
});

App::error(function(Illuminate\Database\Eloquent\ModelNotFoundException $e)
{
   return Response::make(View::make('Main.404'), 404);
});

App::error(function(Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e)
{
	return Response::make(View::make('Main.404'), 404);
});

/*
|--------------------------------------------------------------------------
| Require The Filters File
|--------------------------------------------------------------------------
|
| Next we will load the filters file for the application. This gives us
| a nice separate location to store our route and application filter
| definitions instead of putting them all in the main routes file.
|
*/
require app_path().'/filters.php';

/*
|--------------------------------------------------------------------------
| Bootstrap cache related classes
|--------------------------------------------------------------------------
| 
| Extend laravels file cache with tags functionality and custom cache
| invalidator to accompany it so our filesystem won't choke on 1000s of
| of old cache files left in storage folder.
*/
Cache::extend('taggedFile', function($app)
{
    return new Illuminate\Cache\Repository(new Lib\Extensions\TaggedFileCache);
});

/*
|--------------------------------------------------------------------------
| Register view composers
|--------------------------------------------------------------------------
*/
View::composer('Dashboard.Partials.StatsBar', 'Lib\Composers\DashStatsbarComposer');
View::composer('Dashboard.Menus.Menus', 'Lib\Composers\DashMenusComposer');

Event::subscribe(new Lib\Services\Cache\CacheInvalidator);

ini_set('max_execution_time', 120);

//DB::disableQueryLog();

//bind hybrid auth to the container
App::bind('Hybrid_Auth', function()
{
	return new Hybrid_Auth(Config::get('hybridauth'));
});

$options = App::make('options');
View::share('options', $options);

//load plugins
if(File::exists(public_path('plugins/streaming/plugin/start.php')))
{
	File::requireOnce(public_path('plugins/streaming/plugin/start.php'));
}
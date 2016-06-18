<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{

});


App::after(function($request, $response)
{

});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

// Route::filter('auth', function()
// {
// 	if (Auth::guest()) return Redirect::guest('login');
// });


// Route::filter('auth.basic', function()
// {
// 	return Auth::basic();
// });

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() !== Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});

/**
 * Checks if user has super privilegies (access to everything)
 */
Route::filter('is.admin', function()
{
	if ( ! Helpers::hasAccess('superuser'))
	{	
		return Redirect::to('/');
	}
});

/**
 * Checks if user has super privilegies (acces to everything)
 */
Route::filter('manage', function()
{
	if ( ! Helpers::hasSuperAccess() && ! Helpers::hasAnyAccess(['titles.create', 'titles.edit', 'titles.delete']))
	{
		return Redirect::to('/');
	}
});

/**
 * Checks if specified user is currently logged in user.
 */
Route::filter('is.user', function($route, $request)
{
	$user = Helpers::loggedInUser();
	$id = Helpers::extractId(head( $route->parameters('id') ));
	
	//compare requested profile username with currently logged
	//in users username
	if ( ! $user || (int) $id !== (int) $user->id)
	{
		return Redirect::to('/');
	}
});

Route::filter('increment', function($route, $request)
{
	$title = $route->parameters();
	$id = Helpers::extractId( $title[key($title)] );

	if (key($title) == strtolower(trans('main.people')))
	{
		$table = 'actors';
	}
	else
	{
		$table = 'titles';
	}
	
	DB::table($table)->whereId($id)->increment('views');
});

Route::filter('news', function($route, $request, $value)
{
	if ( ! is_string($value)) App::Abort(403);

	if ( ! Helpers::hasAccess("news.$value"))
	{
		return Redirect::to('news');
	}
});


Route::filter('titles', function($route, $request, $value)
{
    if ( ! is_string($value)) App::Abort(403);

    if ( ! Helpers::hasAccess("titles.$value"))
	{
		return Redirect::to('movies');
	}
});

Route::filter('users', function($route, $request, $value)
{
	if ( ! is_string($value)) App::Abort(403);

	if ( ! Helpers::hasAccess("users.$value"))
	{
		return Redirect::to('/');
	}
});

Route::filter('production_companies', function($route, $request, $value)
{
	if ( ! is_string($value)) App::Abort(403);

	if ( ! Helpers::hasAccess("production_companies.$value"))
	{
		return Redirect::to('/');
	}
});

Route::filter('tv_networks', function($route, $request, $value)
{
	if ( ! is_string($value)) App::Abort(403);

	if ( ! Helpers::hasAccess("tv_networks.$value"))
	{
		return Redirect::to('/');
	}
});

Route::filter('groups', function($route, $request, $value)
{
	if ( ! is_string($value)) App::Abort(403);

	if ( ! Helpers::hasAccess("groups.$value"))
	{
		return Redirect::to('/');
	}
});

Route::filter('view_groups', function($route, $request, $value)
{
	if ( ! Helpers::hasAnyAccess(array("groups.create", "groups.edit", "groups.delete")))
	{
		return Redirect::to('/');
	}
});

Route::filter('slides', function($route, $request, $value)
{
	if ( ! is_string($value)) App::Abort(403);

	if ( ! Helpers::hasAccess("slides.$value"))
	{
		return Redirect::to('dashboard/slider');
	}

});

Route::filter('actions', function($route, $request, $value)
{
	if ( ! is_string($value)) App::Abort(403);

	if ( ! Helpers::hasAccess("actions.$value"))
	{
		return Redirect::to('/');
	}

});

Route::filter('settings', function($route, $request, $value)
{
	if ( ! is_string($value)) App::Abort(403);

	if ( ! Helpers::hasAnyAccess(["settings.$value", 'ads.manage']))
	{
		return Redirect::to('/');
	}
});

Route::filter('ads', function($route, $request, $value)
{
	if ( ! is_string($value)) App::Abort(403);

	if ( ! Helpers::hasAnyAccess(["ads.$value", 'settings.manage']))
	{
		return Redirect::to('/');
	}
});

Route::filter('reviews', function($route, $request, $value)
{
	if ( ! is_string($value)) App::Abort(403);

	if ( ! Helpers::hasAccess("reviews.$value"))
	{
		return Redirect::to('/');
	}

});

Route::filter('slides_create_edit', function()
{
	if(! Helpers::hasAnyAccess(['slides.create', 'slides.edit']))
	{
		return Redirect::to('dashboard/slider');
	}
});

Route::filter('reviews', function($route, $request, $value)
{
    if ( ! is_string($value)) App::Abort(403);

    if ( ! Helpers::hasAccess("reviews.$value"))
	{
		return Redirect::to('/');
	}
});

Route::filter('links', function($route, $request, $value)
{
	if ( ! is_string($value)) App::Abort(403);

	if ( ! Helpers::hasAccess("links.$value"))
	{
		return Redirect::to('/');
	}
});

Route::filter('people', function($route, $request, $value)
{
    if ( ! is_string($value)) App::Abort(403);

    if ( ! Helpers::hasAccess("people.$value"))
	{
		return Redirect::to('people');
	}
});

Route::filter('logged', function()
{
	if ( ! Helpers::loggedInUser())
	{
		Session::put('url.intended', Request::url());
		
		return Request::ajax() ? Response::json('You don\'t have permissions to do that.', 403) : Redirect::to('login');
	}
});

//if we have options table and installed is
//set to trudy value we'll bail with 404
Route::filter('installed', function()
{
	try {
		$hasTable = Schema::hasTable('options');
	} catch (Exception $e) {
		$hasTable = false;
	}

	if ($hasTable)
	{
		$installed = DB::table('options')
						->where('name', 'installed')
						->first();
	
		if ($installed || $installed['value'])
		{
			App::abort(404, 'page not found');
		}
	}
});

//if we have options table and installed is
//set to trudy value we'll bail with 404
Route::filter('updated', function()
{
	if ( ! Helpers::hasAccess('superuser'))
	{	
		App::abort(404);
	}
});
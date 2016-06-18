<?php

Lang::addNamespace('stream', public_path('plugins/streaming/plugin/lang'));

//register plugin routes
require public_path().'/plugins/streaming/plugin/routes.php';

//register a filter for links
Route::filter('links', function($route, $request, $value)
{
    if ( ! is_string($value)) App::Abort(403);

    if ( ! Helpers::hasAccess("links.$value"))
	{
		return Redirect::to('/');
	}
});

//make sure laravel can find and load plugin views
View::addLocation(public_path('plugins/streaming/plugin/views'));

//override mtdb controllers
App::bind('DashboardController', function() {
	return new StreamingDashboardController();
});
App::bind('SeriesSeasonsController', function() {
	return new StreamingSeriesSeasonsController();
});
App::bind('SeasonsEpisodesController', function() {
	return new StreamingSeasonsEpisodesController();
});

//override other classes
App::bind('Lib\Repository', function() {
	return new Plugins\Streaming\Plugin\Lib\Repository;
});
App::bind('Lib\Titles\TitleRepository', function() {
	return new Plugins\Streaming\Plugin\Lib\Titles\TitleRepository;
});

class Title extends Plugins\Streaming\Plugin\Models\Title {}

App::bind('Title', function() {
    return new Title;
});

App::bind('Lib\Categories\CategoriesRepository', function() {
	return new Plugins\Streaming\Plugin\Lib\Categories\StreamingCategoriesRepository;
});

Hooks::registerScript(url('plugins/streaming/assets/js/links.js'));
Hooks::registerScript(url('plugins/streaming/assets/js/create.js'));
Hooks::registerScript(url('plugins/streaming/assets/js/show.js'));

Hooks::registerCss(url('plugins/streaming/assets/css/streaming.css'));

Hooks::registerView('Dashboard.Sidebar');

Hooks::registerView('Titles.Create.Tabs.Panels');
Hooks::registerView('Titles.Create.Tabs.Buttons');
Hooks::registerView('Titles.Index.UnderFilters');
Hooks::registerView('Titles.Index.ForEachMovie');
Hooks::registerReplace('Titles.Seasons.ForEachMovie');
Hooks::registerReplace('Home.ForEachMovie');
Hooks::registerView('Titles.Show.BeforeScripts');
Hooks::registerView('Titles.Show.AfterScripts');

Hooks::RegisterReplace('Titles.Show.LinksPanel');
Hooks::RegisterReplace('Episodes.Show.Jumbotron');





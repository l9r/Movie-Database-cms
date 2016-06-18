<?php namespace Lib\Services\Events;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Lib\Services\Presentation\DbPresenter;
use Actor, App, DB, Event, View, Helpers, User, Title, Groups, News;
 
class EventListeningServiceProvider extends ServiceProvider {

    public function register(){}

    public function boot()
    {      
        //do not allow title updating from external sources
        //once it has been manually updated
        Event::listen('Titles.Modified', function($id)
        {
            DB::table('titles')
                ->where('id', $id)
                ->update(array('allow_update' => 0));
        });

        //do not allow actors updating from external sources
        //once it has been manually updated
        Event::listen('Actor.Updated', function($id)
        {
            DB::table('actors')
                ->where('id', $id)
                ->update(array('allow_update' => 0));
        });
        
        //search results page
        View::composer('Search.Results', function($view)
        {
            //get actors
            $query = preg_replace("/[^A-Za-z0-9]/i", '%', $view->term);
            $results = Actor::where('name', 'like', "%$query%")->limit(18)->get();

            $view->withActors($results);
        });
        
        //dashboard database information boxes
        View::composer('Dashboard.Master', function($view)
        {
            $lastUpdated = News::lastUpdated();

            if ( ! $lastUpdated->isEmpty() )
            {
                $lastUpdated = $lastUpdated->first()->created_at->diffForHumans();
            }
            else
            {
                $lastUpdated = 'Unknown';
            }

            $view->with('userCount', User::all()->count())
                 ->with('movieCount', Title::where('type', '=', 'movie')->count())
                 ->with('seriesCount', Title::where('type', '=', 'series')->count())
                 ->with('newsLastUpdated', $lastUpdated)
                 ->with('actorCount', Actor::count());
        });

    }
 
}
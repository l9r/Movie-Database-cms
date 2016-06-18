<?php namespace Lib\Services\Search;
 
use DB, Exception, App;
use Illuminate\Support\ServiceProvider;
 
class SearchServiceProvider extends ServiceProvider
{
    public function register()
    {

        //ask for search provider from options singleton
        $options = App::make('options');
        $provider = $options->getSearchProvider();

        //get correct implementation namespace
        if ($provider === 'imdb')
        {
            $impl = 'Lib\Services\Search\ImdbSearch';
        }
        elseif ($provider === 'tmdb')
        {
            $impl = 'Lib\Repositories\Data\TmdbData';
        }
        elseif ($provider === 'db')
        {
            $impl = 'Lib\Services\Search\DbSearch';
        }
        else
        {
            $impl = 'Lib\Services\Search\ImdbSearch';
        }

        $this->app->bind(
            'Lib\Services\Search\SearchProviderInterface', $impl
        );
    }
 
}
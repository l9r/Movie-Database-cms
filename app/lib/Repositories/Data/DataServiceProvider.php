<?php namespace Lib\Repositories\Data;
 
use DB, Exception, App;
use Illuminate\Support\ServiceProvider;

 
class DataServiceProvider extends ServiceProvider
{
    public function register()
    {
        //ask for data provider from options singleton
        $options = App::make('options');
        $provider = $options->getDataProvider();
       
        //get correct provider name
        if ($provider === 'imdb')
        {
            $impl = 'ImdbData';
        }
        else
        {
            $impl = 'TmdbData';
        }
 
        $this->app->bind(
            'Lib\Repositories\Data\DataRepositoryInterface',
            "Lib\Repositories\Data\\$impl"
        );
    }
}
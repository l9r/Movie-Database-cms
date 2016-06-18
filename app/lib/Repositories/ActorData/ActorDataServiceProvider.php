<?php namespace Lib\Repositories\ActorData;
 
use DB, Exception, App;
use Illuminate\Support\ServiceProvider;

 
class ActorDataServiceProvider extends ServiceProvider
{
    public function register()
    {
        //ask for data provider from options singleton
        $options = App::make('options');
        $provider = $options->getDataProvider();
       
        //get correct provider name
        if ($provider === 'imdb')
        {
            $impl = 'ImdbActorData';
        }
        else
        {
            $impl = 'TmdbActorData';
        }
 
        $this->app->bind(
            'Lib\Repositories\ActorData\ActorDataRepositoryInterface',
            "Lib\Repositories\ActorData\\$impl"
        );
    }
}
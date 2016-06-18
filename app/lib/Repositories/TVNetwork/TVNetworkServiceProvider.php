<?php namespace Lib\Repositories\TVNetwork;
 
use Illuminate\Support\ServiceProvider;
 
class TVNetworkServiceProvider extends ServiceProvider {
 
    public function register()
    {
        $this->app->bind(
            'Lib\Repositories\User\TVNetworkRepositoryInterface',
            'Lib\Repositories\User\TVNetwork'
        );
    }
 
}
<?php namespace Lib\Repositories\Dashboard;
 
use Illuminate\Support\ServiceProvider;
 
class DashboardServiceProvider extends ServiceProvider {
 
    public function register()
    {
        $this->app->bind(
            'Lib\Repositories\Dashboard\DashboardRepositoryInterface',
            'Lib\Repositories\Dashboard\Dashboard'
        );
    }
 
}
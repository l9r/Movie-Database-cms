<?php namespace Lib\Repositories\ProductionCompany;
 
use Illuminate\Support\ServiceProvider;
 
class ProductionCompanyServiceProvider extends ServiceProvider {
 
    public function register()
    {
        $this->app->bind(
            'Lib\Repositories\User\ProductionCompanyRepositoryInterface',
            'Lib\Repositories\User\ProductionCompany'
        );
    }
 
}
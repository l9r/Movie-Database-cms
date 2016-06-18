<?php namespace Lib\Repositories\Group;
 
use Illuminate\Support\ServiceProvider;
 
class GroupServiceProvider extends ServiceProvider {
 
    public function register()
    {
        $this->app->bind(
            'Lib\Repositories\Group\GroupRepositoryInterface',
            'Lib\Repositories\Group\SentryGroup'
        );
    }
 
}
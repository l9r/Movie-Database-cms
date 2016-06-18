<?php namespace Lib\Repositories\User;
 
use Illuminate\Support\ServiceProvider;
 
class UserServiceProvider extends ServiceProvider {
 
    public function register()
    {
        $this->app->bind(
            'Lib\Repositories\User\UserRepositoryInterface',
            'Lib\Repositories\User\SentryUser'
        );
    }
 
}
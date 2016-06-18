<?php

use Laravel\Cashier\BillableTrait;
use Laravel\Cashier\BillableInterface;
use Cartalyst\Sentry\Users\Eloquent\User as SentryUser;

class User extends Entity implements BillableInterface
{
    use BillableTrait;

	public $table = 'users';

    protected $dates = ['trial_ends_at', 'subscription_ends_at'];

    protected $hidden = array('password', 'persist_code', 'reset_password_code');

	public function title()
    {
        return $this->belongsToMany('Title', 'users_titles')->withPivot('favorite', 'watchlist');
    }
    public function group()
    {
        return $this->belongsToMany('Group', 'users_groups');
    }

    public function reviews()
    {
        return $this->hasMany('Review', 'author');
    }

    public function setPasswordAttribute($value)
    {
        if ( ! $value) return;

        $hash = App::make('Cartalyst\Sentry\Hashing\NativeHasher');

        $this->attributes['password'] =  $hash->hash($value);
    }

    /**
     * Wetches titles user has added to specified list.
     * 
     * @param  Builder 	  $query
     * @param  SentryUser $user
     * @param  string     $name
     * @return array
     */
    public function scopeFetchLists($query, SentryUser $user)
    {
    	$user = $query->with('title')->findOrFail($user->id);
       
    	return $this->compileList($user->title);
    }

    /**
     * Compiles users titles into id => title array.
     * 
     * @param  Collection $titles
     * @return array
     */
    private function compileList($titles)
    {
    	foreach ($titles as $k => $v)
    	{
    		if ($v->pivot->favorite)
    		{
    			$favorites[$v->id] = $v->title; 
    		}

    		if ($v->pivot->watchlist)
    		{
    			$watchlist[$v->id] = $v->title; 
    		}
    	}

    	$favorites = ( isset($favorites) ? $favorites : array());
    	$watchlist = ( isset($watchlist) ? $watchlist : array());

    	return array( 'watchlist' => $watchlist, 'favorites' => $favorites );
    }

	
}
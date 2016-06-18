<?php

class Social extends Eloquent
{
	protected $table = 'social';

    /**
     * Checks if provided identifier and provider is already
     * authenticated in the database.
     * 
     * @param  string     $ident
     * @param  string     $provider
     * @return array
     */
    public function alreadyAuthenticated($ident, $provider)
    {
        $social = $this->where('service_user_identifier', $ident)
                       ->where('service', $provider)
                       ->first();

        if ($social)
        {
            return User::find($social->user_id);
        }

        return null;
    }
}
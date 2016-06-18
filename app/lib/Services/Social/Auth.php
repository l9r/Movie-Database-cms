<?php namespace Lib\Services\Social;

use Social, Sentry, User, Redirect;

class Auth {

	/**
	 * Hybrid authentication instance.
	 * 
	 * @var Hybrid_Auth
	 */
	public $hybrid;

	/**
	 * Stores user profile.
	 * 
	 * @var Object
	 */
	public $profile;

	/**
	 * Stores the service user is trying
	 * to authenticate with.
	 * 
	 * @var string
	 */
	public $service;

	public function __construct(\Hybrid_Auth $hybrid)
	{
		$this->hybrid = $hybrid;
	}

	/**
	 * Logs the user in with specified provider.
	 *
	 * @param  string $service
	 * 
	 * @return mixed
	 */
	public function login($service)
	{
		$valid = array('twitter', 'facebook', 'google');
		if ( ! in_array($service, $valid)) return;

		$this->service = $service;
		
		$provider = $this->authenticate();
		$this->profile = $provider->getUserProfile();

		if ($provider && is_a($provider, 'Hybrid_Provider_Adapter'))
	    {
	    	$user = $this->checkIfAlreadyAuthenticated();

	    	if ($user && is_a($user, 'User'))
	    	{
	    		return $this->loginWithSentry($user);
	    	}
	    	else
	    	{
	    		//request email if logging in with twitter
			    if ($this->service == 'twitter')
			    {
			    	return array('twitter' => 'request.mail');
			    }

	    		return $this->registerWithSentry();
	    	}
	    }
	}

	/**
	 * Register user with sentry.
	 * 
	 * @return mixed
	 */
	private function registerWithSentry()
	{
    	//check if email not taken
    	$email = User::where('email', $this->profile->email)->first();

	    if ($email)
	    {
	    	return array('error' => trans('main.social email taken', array('service' => $this->service)));
	    }

	    $user = $this->createProfile();
	   	$this->linkProfileWithIdentifier();
	   	$this->loginWithSentry($user);

	   	return true;
	}

	/**
	 * associate user profile with service name and user identifier.
	 *
	 * @param $email mixed
	 * @return void
	 */
	public function linkProfileWithIdentifier($email = null)
	{	
		$email = $email ? $email : $this->profile->email;

		$id = User::whereEmail($email)->first()->id;

	    $social = new Social;
	    $social->service = $this->service;
	    $social->service_user_identifier = $this->profile->identifier;
	    $social->user_id = $id;
	    $social->save();
	}

	/**
	 * Creates user profile.
	 * 
	 * @param  mixed $email
	 * @return User
	 */
	public function createProfile($email = null)
	{
		$user = new User;
	    $user->username = str_replace(' ', '', $this->profile->displayName);
	    $user->password = str_random(15);
	    $user->email = $email ? $email : $this->profile->email;
	    $user->first_name = $this->profile->firstName;
	    $user->last_name = $this->profile->lastName;
	    $user->gender = $this->profile->gender;
	    $user->avatar = $this->profile->photoURL;
	    $user->activated = 1;
	   	$user->save();

	   	return $user;
	}

	/**
	 * Logs the user in using sentry.
	 * 
	 * @param  User $user
	 * @return Redirect
	 */
	public function loginWithSentry($user)
	{
    	$sentryUser = Sentry::findUserById($user->id);

	    Sentry::login($sentryUser, true);

	    return true;
	}

	/**
	 * checks if user is already identified with this service
	 * returns User object if so, null otherwise.
	 *
	 * @return mixed
	 */
	private function checkIfAlreadyAuthenticated()
	{		
	    $ident = $this->profile->identifier;

	    $social = new Social;
	    $user   = $social->alreadyAuthenticated($ident, $this->service);

	    return $user;
	}

	/**
	 * Authenticate user with provided service.
	 * 
	 * @return Hybrid_Provider_Adapter
	 */
	public function authenticate()
	{
	    return $this->hybrid->authenticate($this->service);
	}

	/**
	 * Log the user out with all providers.
	 * 
	 * @return void
	 */
	public function logout()
	{
		$this->hybrid->logoutAllProviders();
	}
}
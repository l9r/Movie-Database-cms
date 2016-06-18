<?php

use Lib\Services\Social\Auth;
use Lib\Services\Validation\LoginValidator;

class SessionController extends BaseController {

	/**
	 * Validator instance.
	 * 
	 * @var Lib\Services\Validation\LoginValidator
	 */
	private $validator;

	/**
	 * Hybrid authentication instance.
	 * 
	 * @var Hybrid_Auth
	 */
	private $hybrid;

	/**
	 * Holds social newtwork user profile.
	 * 
	 * @var Object.
	 */
	private $profile;

	/**
	 * Social authentication manager instance.
	 * 
	 * @var Lib\Services\Social\Auth
	 */
	private $social;

	/**
	 * Social login response.
	 * 
	 * @var mixed
	 */
	private $response;

	public function __construct(LoginValidator $validator, Hybrid_Auth $hybrid, Auth $social)
	{
		$this->social = $social;
		$this->hybrid = $hybrid;
		$this->validator = $validator;
	}

	/**
	 * Create new season (log the user in)
	 *
	 * @return Response
	 */
	public function create()
	{
		//store the refferer so we can redirect to the
		//intended page after login
		if ( ! str_contains(URL::previous(), 'register'))
		{
			Session::put('url.intended', URL::previous());
		}
		
		//if user is already logged in redirect home
		if (Sentry::check()) return Redirect::to('/');

		return View::make('Users.Login');
	}

	/**
	 * Logs the user in.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::except('_token');
		if ( ! isset($input['remember'])) $input['remember'] = false;	

		if ( ! $this->validator->with($input)->passes())
		{
			return Redirect::back()->withErrors($this->validator->errors())->withInput($input);
		}

		try
		{
			$credentials = array('username' => $input['username'], 'password' => $input['password']);

			Sentry::authenticate($credentials, $input['remember']);
		}

		catch (Cartalyst\Sentry\Users\WrongPasswordException $e)
		{
		    $messages = array('username' => 'Username and password do not match.');
		}
		catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
		{		    
		    $messages = array('username' => 'Username and password do not match.');
		}
		catch (Cartalyst\Sentry\Users\UserNotActivatedException $e)
		{
		    $messages = array('username' => 'This user is not activated.');
		}
		catch (Cartalyst\Sentry\Throttling\UserSuspendedException $e)
		{
		   $messages = array('username' => 'This user is suspended.');
		}
		catch (Cartalyst\Sentry\Throttling\UserBannedException $e)
		{
		    $messages = array('username' => 'This user is banned');
		}

		if ( ! empty($messages) )
		{
			return Redirect::back()->withInput()->withErrors($messages);
		}

		return Redirect::intended();
	}

	/**
	 * Logs the use with twitter, fb, or google.
	 * 
	 * @param  string $action [description]
	 * @return Redirect
	 */
	public function social($action = 'facebook')
	{
	    //if user is already logged in redirect home
		if (Sentry::check()) return Redirect::to('/');

	    // fix for redirect loop
	    if ($action == "auth") {
	        try {
	            Hybrid_Endpoint::process();
	        }
	        catch (Exception $e) {
	            return Redirect::route('hybridauth');
	        }
	        return;
	    }

	    $this->response = $this->social->login($action);

	    //successfully logged user in
	    if ($this->response && ! is_array($this->response))
	    {
	    	return Redirect::intended();
	    }

	    //if response is array and key twitter exists we're logging in
	    //with twitter, we'll need to ask user for email as twitter
	    //doesnt provide it.
	    elseif (isset($this->response['twitter']))
	    {
	    	return View::make('Users.TwitterEmail');
	    }
	    elseif (isset($this->response['error']))
	    {
	    	return Redirect::to('login')->withFailure($this->response['error']);
	    }
	    
	    return Redirect::to('login')->withFailure( trans('main.problem with social login') );
	}

	/**
	 * Logs the user in after he provides his email
	 * and authenticates with twitter.
	 * 
	 * @return Redirect
	 */
	public function twitterEmail()
	{
		$email = Input::get('email');

		//check if email already exists
		$exists = User::where('email', $email)->get();
	
		if ( ! $exists->isEmpty() || ! $email)
		{
			return View::make('Users.TwitterEmail')->withFailure( trans('main.email exists') );
		}
		
		$auth = App::make('Lib\Services\Social\Auth');

		$provider = $this->social->hybrid->authenticate('twitter');
		$this->social->profile = $provider->getUserProfile();
		$this->social->service = 'twitter';
		$user = $this->social->createProfile($email);
	   	$this->social->linkProfileWithIdentifier($email);
	   	$this->social->loginWithSentry($user);

	    return Redirect::intended();
	}

	/**
	 * Logs the user out.
	 * 
	 * @return redirect
	 */
	public function logOut()
	{
		Sentry::logout();
		$this->social->logout();

		return Redirect::to('/');
	}

}
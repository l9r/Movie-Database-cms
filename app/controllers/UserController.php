<?php

use Carbon\Carbon;
use Lib\Services\Validation\UserValidator;
use Lib\Repositories\User\UserRepositoryInterface as Repo;

class UserController extends \BaseController {

	/**
	 * User validator instance.
	 * 
	 * @var Lib\Services\Validation\UserValidator
	 */
	private $registerValidator;

	/**
	 * User repository instance.
	 * 
	 * @var Lib\Repositories\User\UserRepositoryInterface
	 */
	private $user;

	/**
	 * Options instance.
	 * 
	 * @var Lib\Services\Options\Options
	 */
	private $options;

	/**
	 * Apply filters and innstantiate dependencies.
	 */
	public function __construct(UserValidator $validator, Repo $user)
	{
		$this->beforeFilter('csrf', array('on' => 'post'));
		$this->beforeFilter('is.admin', array('only' => array('ban', 'unban', 'assignToGroup')));
		$this->beforeFilter('is.user', array('only' => array('edit', 'changePassword')));
		$this->beforeFilter('logged', array('only' => array('paginate')));

		$this->beforeFilter('users:create', array('only' => array('createNew')));
		$this->beforeFilter('users:delete', array('only' => array('destroy')));
		$this->beforeFilter('users:edit', array('only' => array('update')));

		$this->user = $user;
		$this->validator = $validator;

		$this->options = App::make('options');
	}

	/**
	 * Return users for pagination.
	 * 
	 * @return JSON
	 */
	public function paginate()
	{
		return $this->user->paginate(Input::except('_token'));
	}

	/**
	 * Displays registration view.
	 *
	 * @return View.
	 */
	public function create()
	{
		return View::make('Users.Register');		
	}

	/**
	 * Stores new user in database.
	 *
	 * @return View.
	 */
	public function store()
	{
		$input = Input::except('_method', '_token', 'submit');
		
		if ( ! $this->validator->with($input)->passes())
		{
			return Redirect::back()->withErrors($this->validator->errors())->withInput($input);
		}

		if ($this->options->requireUserActivation())
		{
			$this->user->register($input);
		    
			return Redirect::back()->withSuccess( trans('users.registered successfully') );
		}
		
		$this->user->register($input, true);
		    
		return Redirect::back()->withSuccess( trans('users.registered successfully no act') );
	}

	/**
	 * Create a new user. Admin only.
	 * 
	 * @return JSON
	 */
	public function createNew()
	{
		$input = Input::except('_token');
		
		if ( ! $this->validator->with($input)->passes())
		{
			return Response::json($this->validator->errors(), 400);
		}

		$this->user->register($input, true);

		return Response::json('User created successfully', 201);
	}

	/**
	 * Activates provided user.
	 *
	 * @param  string $id user id
	 * @param  string $code activation code
	 * 
	 * @return void
	 */
	public function activate($id, $code)
	{
		try
		{
			$this->user->activate( e($id), e($code) );
		}
		catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
			return Redirect::to('/')->withInfo( trans('users.not found or already activated') );
		}
		catch (Cartalyst\Sentry\Users\UserAlreadyActivatedException $e)
		{
			return Redirect::to('/')->withInfo( trans('users.not found or already activated') );
		}

		return Redirect::to('/')->withSuccess( trans('users.activated successfully') );
	}

	/**
	 * Shows specified users profile.
	 *
	 * @param  int  $id
	 * @return View.
	 */
	public function show($name)
	{		
		$data = $this->user->prepareProfile($name);

		return View::make('Users.Show')->withUser($data['user'])
		                               ->with('favCount', $data['favCount'])
		                               ->with('revCount', $data['revCount'])
		                               ->with('watCount', $data['watCount']);
	}

	/**
	 * Show the form for editing user information.
	 *
	 * @param  string $username
	 * @return View
	 */
	public function edit($name)
	{
		$user = $this->user->byUri($name);

		return View::make('Users.Edit')->withUser($user);
	}

	/**
	 * Uploads and associates user avatar.
	 * 
	 * @param  string $username
	 * @return void
	 */
	public function avatar($username)
	{
		$input = array('avatar' => Input::file('avatar'));

		if ( ! $this->validator->setRules('avatar')->with($input)->passes())
		{
			return Redirect::back()->withErrors($this->validator->errors());
		}

		$this->user->uploadAvatar($input, $username);

		return Redirect::back()->withSuccess( trans('users.uploaded avatar success') );
	}

	/**
	 * Uploads and associates user profile background.
	 * 
	 * @param  string $id
	 * @return void
	 */
	public function background($id)
	{
		$input = array('bg' => Input::file('bg'));

		if ( ! $this->validator->setRules('background')->with($input)->passes())
		{
			return Redirect::back()->withErrors($this->validator->errors());
		}

		$this->user->uploadBg($input, $id);

		return Redirect::back()->withSuccess( trans('users.uploaded avatar success') );
	}

	/**
	 * Update users general information.
	 *
	 * @param  string  $username
	 * @return Redirect
	 */
	public function update($username)
	{
		$user = $this->user->byUsername($username);

		$input = Input::except('_method', '_token', 'password', 'password_confirmation');

		if ( ! $this->validator->setRules('editInfo')->with($input)->passes())
		{
			return Redirect::back()->withErrors($this->validator->errors());	
		}

		$this->user->update($user, $input);
		\Cache::flush();
		if (Request::ajax())
		{
			return Response::json(trans('users.update success'), 200);
		}

		return Redirect::to(Helpers::url($user->username, $user->id, 'users'))->withSuccess( trans('users.update success') );
	}

	/**
	 * Displays a page for changing password.
	 * 
	 * @param  string $username
	 * @return View
	 */
	public function changePassword($username)
	{
		$user = $this->user->byUri($username);
		
		return View::make('Users.ChangePassword')->withUser($user);
	}

	/**
	 * Stores new user password in database.
	 * 
	 * @param  string $username
	 * @return void
	 */
	public function storeNewPass($username)
	{
		$user = Sentry::findUserByLogin($username);

		$input = Input::except('_token');

		$this->validator->rules = array(
			'new_password' => 'required|confirmed|min:5|max:30',
			'old_password' => 'required|min:5|max:30');

		if ( ! $this->validator->with($input)->passes())
		{
			return Redirect::back()->withErrors($this->validator->errors());
		}

		if ( ! $user->checkPassword( $input['old_password']) )
		{
			return Redirect::back()->withErrors(array('old_password' => trans('users.password didnt match')));
		}

		$this->user->changePassword($input, $username);

		return Redirect::to('/')->withSuccess( trans('users.changed pass success') );
	}

	/**
	 * Deletes user and related records from database.
	 *
	 * @param  int  $id
	 * @return Redirect
	 */
	public function destroy($id)
	{
		if (Sentry::getUser()->id == $id)
		{
			return Response::json(trans('users.can\'t delete account you\'re logged in with'), 400);
		}

		try
		{	    
		    $this->user->delete($id);		   
		}
		catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
		    return Response::json( trans('users.user not found'), 404);
		}

		return Response::json(trans('users.user deleted successfully'), 200);
	}

	/**
	 * Bans the specified user.
	 * 
	 * @param  string $id
	 * @return Redirect
	 */
	public function ban($id)
	{
		if ($this->user->ban( e($id) ))
		{
			return Redirect::back()->withSuccess( trans('users.banned successfully', array('id' => $id)) );
		}
		else
		{
			return Redirect::back()->withFailure( trans('users.ban failed', array('id' => $id)) );
		}	
	}

	/**
	 * Unbans the specified user.
	 * 
	 * @param  string $id username
	 * @return redirect with response
	 */
	public function unban($login)
	{
		$this->user->unban( e($login) );

		return Redirect::back()->withSuccess( trans('users.unbanned successfully', array('id' => $login)) );
	}

	/**
	 * Assigns specified group to the specified user.
	 * 
	 * @param  string $login
	 * @return Redirect
	 */
	public function assignToGroup($login)
	{
		$input = Input::except('_token');

		$this->user->assignGroup($input, e($login));

		return Redirect::back()->withSuccess( trans('users.group assigned') );
	}

	/**
	 * Displays view for requesting a password reset.
	 * 
	 * @return Redirect/View
	 */
	public function requestPassReset()
	{
		if (Sentry::check())
		{
			return Redirect::to('/')->withInfo( trans('users.already logged in') );
		}

		return View::make('Users.ResetPassword');
	}

	/**
	 * Sends passowrd reset email.
	 * 
	 * @return Redirect
	 */
	public function sendPasswordReset()
	{
		$input = Input::except('_token');

		$this->validator->rules = array('email' => 'required|email|max:40|exists:users,email');

		if ( ! $this->validator->with($input)->passes())
		{
			return Redirect::back()->withErrors($this->validator->errors())->withInput($input);
		}
		 
		$this->user->sendPassReset($input);

		return Redirect::to('/')->withSuccess( trans('users.reset email sent') );
	}

	/**
	 * Display user favorite titles page.
	 * 
	 * @param  string $name
	 * @return View
	 */
	public function showFavorites($name)
	{
		$data = $this->user->prepareProfile('favorite', $name, Input::all());

		return View::make('Users.Profile')->withUser($data['user'])
		                                  ->withWatchlist($data['watchlist'])
		                                  ->withFavorite($data['favorite'])
		                                  ->withReviews($data['reviews'])
		                                  ->with('revCount', $data['revCount'])
		                                  ->with('favCount', $data['favCount'])
		                                  ->with('watCount', $data['watCount']);
	}

	/**
	 * Display user review page.
	 * 
	 * @param  string $name
	 * @return View
	 */
	public function showReviews($name)
	{
		$data = $this->user->prepareProfile('favorite', $name, Input::all());

		return View::make('Users.Reviews')->withUser($data['user'])
		                                  ->withWatchlist($data['watchlist'])
		                                  ->withFavorite($data['favorite'])
		                                  ->withReviews($data['reviews'])
		                                  ->with('revCount', $data['revCount'])
		                                  ->with('favCount', $data['favCount'])
		                                  ->with('watCount', $data['watCount']);
	}

	/**
	 * Resets user password.
	 * 
	 * @param  string $code
	 * @return Redirect
	 */
	public function resetPassword($code)
	{
		$new = str_random(20);

		try
		{
			$user = Sentry::findUserByResetPasswordCode( e($code) );
		}
		catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
		   return Redirect::to('/')->withFailure( trans('users.invalid reset code') );
		}

		if ( $this->user->resetPassword($user, e($code), $new))
		{
			$data = array('username' => $user->username, 'email' => $user->email, 'password' => $new);

			$this->user->sendNewPassword($data);
			
			return Redirect::to('/')->withSuccess( trans('users.pass reset success') );

			Event::fire('User.PasswordReset', array($user->username, Carbon::now()));
		}

		return Redirect::to('/')->withFailure( trans('users.pass reset failure') );
	}


}
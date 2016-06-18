<?php namespace Lib\Repositories\User;

use Carbon\Carbon;
use Lib\Repository;
use Lib\Services\Mail\Mailer;
use Lib\Services\Images\ImageSaver;
use User, Event, Sentry, Redirect, Paginator, App, Helpers, DB;

class SentryUser extends Repository implements UserRepositoryInterface
{
	/**
	 * User model instance.
	 * 
	 * @var User
	 */
	protected $model;

	/**
	 * Mailer instance.
	 * 
	 * @var Lib\Services\Mail\Mailer
	 */
	private $mailer;

	/**
	 * Images handler instance.
	 * 
	 * @var Lib\Services\Images\ImageSaver
	 */
	private $images;

	public function __construct(User $user, Mailer $mailer, ImageSaver $images)
	{
		$this->model   = $user;
		$this->mailer = $mailer;
		$this->images = $images;
	}

	/**
	 * Registers user.
	 * 
	 * @param  array $input
	 * @param  boolean $act
	 * @return void
	 */
	public function register($input, $act = false)
	{
		$groupId = null;
		if(isset($input['group_id']))
		   $groupId = $input['group_id'];

		$input =  array_except($input, 'group_id');
		$user = Sentry::register($this->prepareInput($input), $act);

		if($groupId)
		{
			$group = Sentry::findGroupById($groupId);
			if($group)
			{
				\DB::table('users_groups')->insert(['user_id' => $user->id, 'group_id' => $groupId]);
			}
		}

		Event::fire('Users.Registered', array($user['username'], Carbon::now(), 'Registered'));

		if ( ! $act)
		{
			$code = $user->getActivationCode();
			$user['code'] = $code;
			$this->mailer->send('Emails.Activation', $user->toArray(), null, trans('main.account activation') );
		}

		Event::fire("eloquent.saved: User", array(new User));
	}

	/**
	 * Do any needed preparations on user input before passing to sentry.
	 * 
	 * @param  array  $input
	 * @return array
	 */
	private function prepareInput(array $input)
	{
		/*if (isset($input['permissions']) && is_string($input['permissions'])) {

			try {
				$decoded = json_decode($input['permissions'], true);
				$input['permissions'] = $decoded ? $decoded : array();
			} catch (\Exception $e) {
				$input['permissions'] = array();
			}
		}*/

		return array_except($input, ['password_confirmation', 'permissions', 'groups']);
	}

	/**
	 * Activates user account.
	 * 
	 * @param  string $id
	 * @param  string $code
	 * @return void
	 */
	public function activate($id, $code)
	{	
	    $user = Sentry::findUserById( e($id) );

	    if ($user->attemptActivation( e($code) ))
	    {
	        Event::fire('Users.Activated', array($id, Carbon::now(), 'Activated'));

	        return Redirect::to('/')->withSuccess('Your account was activated successfully!');
	    }
	    else
	    {
	        return Redirect::to('/')->withFailure('Wrong activation code.');
	    }
	}

	/**
	 * Deletes the provided user from database.
	 * 
	 * @param  mixed $id
	 * @return void
	 */
	public function delete($id)
	{
		$user = Sentry::findUserById($id);

		$user->delete();

		Event::fire('Users.Deleted', array($id, Carbon::now(), 'Deleted'));

	}

	/**
	 * Bans the specified user.
	 * 
	 * @param  string $id
	 * @return boolean
	 */
	public function ban($id)
	{
		$user = Sentry::findUserByLogin($id);

		if ($user->isSuperUser() || $user->id == Helpers::loggedInUser()->id)
		{
			return false;
		}

		$throttle = Sentry::findThrottlerByUserId($user->id);
		$throttle->ban();

		Event::fire('Users.Banned', array($id, Carbon::now(), 'Banned'));

		return true;
	}

	/**
	 * Updates user information from input.
	 * 
	 * @param  User   $user
	 * @param  array  $input
	 * @return void
	 */
	public function update(User $user, array $input)
	{
		$groupId = $input['group_id'];
		$input = array_except($input, 'group_id');
		foreach ($input as $k => $v)
		{
			$user->$k = $v;
		}

		$user->save();

		if($groupId)
		{
			$group = Sentry::findGroupById($groupId);
			if($group)
			{
				\DB::table('users_groups')->where('user_id', $user->id)->delete();
				\DB::table('users_groups')->where('user_id', $user->id)->insert(['user_id' => $user->id, 'group_id' => $groupId]);
			}
		}

		Event::fire("eloquent.saved: User", array(new User));
	}

	/**
	 * Unbans specified user.
	 * 
	 * @param  string $login
	 * @return void
	 */
	public function unban($login)
	{
		$user = Sentry::findUserByLogin($login);

		Sentry::findThrottlerByUserId($user->id)->Unban();

		Event::fire('Users.Unbanned', array($login, Carbon::now(), 'Unbanned') );
	}

	/**
	 * Assigns specified group to specified user.
	 * 
	 * @param  array $input 
	 * @param  string $login
	 * @return void
	 */
	public function assignGroup($input, $login)
	{
		if ( isset($input['group']) )
		{
			$group = Sentry::findGroupByName( $input['group'] );
			$user = Sentry::findUserByLogin($login);

			$user->addGroup($group);

			Event::fire('Users.GroupAssigned', array($login, Carbon::now(), 'Was Assigned a Group'));
		}	
	}

	/**
	 * Get users watchlist/favorites by title id.
	 * 
	 * @param  string/int $id
	 * @return array
	 */
	public function getListsByTitleId($id)
	{
		if ($user = Sentry::getUser())
		{
			return DB::table('users_titles')->where('user_id', $user->id)->where('title_id', Helpers::extractId($id))->get();
		}
		
		return array();
	}

	/**
	 * Sends password reset email.
	 * 
	 * @param  array $input
	 * @return void
	 */
	public function sendPassReset(array $input)
	{
		//we'll need to find user using empty model instance since login
		//is now not email but username, and sentry doesn't provide helper
		//for searching by email
		$empty = Sentry::getUserProvider()->getEmptyUser();
		$user = $empty->where('email', '=', $input['email'])->first();

		$code = $user->getResetPasswordCode();
		$user = array('email' => $user->email, 'username' => $user->username, 'code' => $code);

		$this->mailer->send('Emails.ForgotPassword', $user, null, trans('users.reset email subject'));			
	}

	/**
	 * Resets provided users password.
	 * 
	 * @param  array  $user
	 * @param  string $code reset code
	 * @param  string $new  new password
	 * @return boolean/void
	 */
	public function resetPassword($user, $code, $new)
	{
		if ($user->attemptResetPassword($code, $new))
	    {		
			return true;
	    }	    
	}

	/**
	 * Sends provided user an email with new password.
	 * 
	 * @param  array $data
	 * @return void
	 */
	public function sendNewPassword($data)
	{
		$this->mailer->send('Emails.NewPassword', $data, null, trans('users.new pass email subject'));
	}

	/**
	 * Uploads provided avatar and associates with user.
	 * 
	 * @param  array  $input
	 * @param  string $id
	 * @return void
	 */
	public function uploadAvatar(array $input, $id)
	{
		$user = User::find($id);

		$paths['big'] = "avatars/$id.jpg";
		$paths['small'] = "avatars/$id.small.jpg";

		$this->images->saveAvatar($input, $paths);

		$user->avatar = $paths['big'];
		$user->save();
	}

	/**
	 * Uploads provided background and associates with user.
	 * 
	 * @param  array  $input
	 * @param  string $id
	 * @return void
	 */
	public function uploadBg(array $input, $id)
	{
		$user = User::find($id);

		$path = "avatars/bgs/$id.jpg";

		$this->images->saveBg($input, $path);

		$user->background = $path;
		$user->save();
	}

	/**
	 * Changes user password.
	 * 
	 * @param  array  $input
	 * @param  string $username
	 * @return void
	 */
	public function changePassword(array $input, $username)
	{
		$empty = Sentry::getUserProvider()->getEmptyUser();
		$user = $empty->where('username', '=', $username)->firstOrFail();

 		$user->password = $input['new_password'];
 		$user->save();

 		Event::fire('Users.PasswordChanged', array($username, Carbon::now(), 'Changed Password'));
	}

	/**
	 * Fetches user by username.
	 * 
	 * @param  string $name
	 * @return User
	 */
	public function byUsername($name)
	{
		return $this->model->whereUsername($name)->firstOrFail();
	}

	/**
	 * Fetches user by id-username string.
	 * 
	 * @param  string $name
	 * @return User
	 */
	public function byUri($name)
	{
		$id = Helpers::extractId($name);

		return $this->model->find($id);
	}

	/**
	 * gets users watchlist, slice out 8 most recent additions and paginate.
	 * 
	 * @param  string $list
	 * @param  string $name
	 * @param  array  $input
	 * @return array
	 */
	public function prepareProfile($name)
	{
		$user = $this->model->findOrFail(Helpers::extractId($name));

		$favCount = DB::table('users_titles')->where('user_id', $user->id)->where('favorite', 1)->count();
		$watCount = DB::table('users_titles')->where('user_id', $user->id)->where('watchlist', 1)->count();
		$revCount = DB::table('reviews')->where('user_id', $user->id)->count();

		return array('user' => $user, 'watCount' => $watCount, 'favCount' => $favCount, 'revCount' => $revCount);
	}

}
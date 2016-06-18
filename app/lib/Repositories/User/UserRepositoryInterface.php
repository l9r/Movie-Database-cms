<?php namespace Lib\Repositories\User;

use User;

interface UserRepositoryInterface
{
	
	/**
	 * Registers a user.
	 * 
	 * @param  array $input
	 * @param  boolean $act
	 * @return self
	 */
	public function register($input, $act = false);

	/**
	 * Activates user account.
	 * 
	 * @param  string $id
	 * @param  string $code
	 * @return void
	 */
	public function activate($id, $code);

	/**
	 * Deletes the provided user from database.
	 * 
	 * @param  string $username
	 * @return  void.
	 */
	public function delete($username);

	/**
	 * Bans the specified user.
	 * 
	 * @param  string $id
	 * @return void
	 */
	public function ban($id);

	/**
	 * Updates user information from input.
	 * 
	 * @param  User   $user
	 * @param  array  $input
	 * @return void
	 */
	public function update(User $user, array $input);

	/**
	 * Unbans specified user.
	 * 
	 * @param  string $login
	 * @return void
	 */
	public function unban($login);

	/**
	 * Assigns specified group to specified user.
	 * 
	 * @param  array $input 
	 * @param  string $login
	 * @return void
	 */
	public function assignGroup($input, $login);

	/**
	 * Sends password reset email.
	 * 
	 * @param  array $input
	 * @return void
	 */
	public function sendPassReset(array $input);

	/**
	 * Resets provided users password.
	 * 
	 * @param  array  $user
	 * @param  string $code reset code
	 * @param  string $new  new password
	 * @return boolean/void
	 */
	public function resetPassword($user, $code, $new);

	/**
	 * Sends provided user an email with new password.
	 * 
	 * @param  array $data
	 * @return void
	 */
	public function sendNewPassword($data);

	/**
	 * Uploads provided avatar and associates with user.
	 * 
	 * @param  array  $input
	 * @param  string $username
	 * @return void
	 */
	public function uploadAvatar(array $input, $username);


	/**
	 * Changes user password.
	 * 
	 * @param  array  $input
	 * @param  string $username
	 * @return void
	 */
	public function changePassword(array $input, $username);

	/**
	 * Fetches user by username.
	 * 
	 * @param  string $name
	 * @return User
	 */
	public function byUsername($name);

	/**
	 * gets users watchlist, slice out 8 most recent additions and paginate.
	 * 
	 * @param  string $name
	 * @return array
	 */
	public function prepareProfile($name);

}
<?php namespace Lib\Services\Validation;

class UserValidator extends AbstractValidator
{
	public $rules = array(
		'username'    => 'required|alpha_num|min:3|max:20|unique:users',
		'email'	      => 'required|email|max:40|unique:users',
		'password'    => 'required|confirmed|min:5|max:30'
		);

	protected $messages = array(
		'exists' => 'Could not find a user with information provided.'
		);

	protected $editInfo = array(
		'gender'	  => 'in:male,female',
		'first_name'  => 'alpha_num|min:2|max:100',
		'last_name'   => 'alpha_num|min:2|max:100'
		);

	protected $avatar = array('avatar' => 'required|image|max:3072|mimes:jpeg,jpg,gif,png');

	protected $background = array('bg' => 'required|image|max:3072|mimes:jpeg,jpg,gif,png');

	protected $miniProfile = array('username' => 'required|alpha_num|min:3|max:20|exists:users');

	/**
	 * Changes default rules on validator.
	 * 
	 * @param string $name
	 */
	public function setRules($name)
	{
		$this->rules = $this->$name;

		return $this;
	}
}
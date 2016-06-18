<?php namespace Lib\Services\Validation;

class LoginValidator extends AbstractValidator
{
	public $rules = array(
		'username' => 'required|alpha_num|min:3|max:20',
		'password' => 'required|min:5|max:30'
		);
}
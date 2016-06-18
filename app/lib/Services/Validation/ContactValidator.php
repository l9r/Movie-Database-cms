<?php namespace Lib\Services\Validation;

class ContactValidator extends AbstractValidator
{
	public $rules = array(
		'name'    => 'required|min:3|max:20|',
		'email'	  => 'required|email|max:40|',
		'comment' => 'required|min:20|max:800',
		'captcha' => 'required|captcha',
		);

	protected $messages = array('captcha' => 'The captcha string you entered is incorrect, please try again.');
}
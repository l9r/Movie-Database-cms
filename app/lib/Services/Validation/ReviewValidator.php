<?php namespace Lib\Services\Validation;

class ReviewValidator extends AbstractValidator
{
	public $rules = array(
		'body'   => 'required|min:50|max:800',
		'score'	 => 'required|in:1,2,3,4,5,6,7,8,9,10|max:2',
	);

	protected $messages = array(
		'score.required'    => 'Please select your rating.',
		'body.required'     => 'Please enter something.',
		'body.min'     		=> 'Your review should be atleast 50 characters long',
		'body.max'     		=> 'Your review should not be longer then 800 characters',
	);
}
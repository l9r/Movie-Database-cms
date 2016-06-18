<?php namespace Lib\Services\Validation;

class GroupValidator extends AbstractValidator
{
	public $rules = array(

			'name'           => 'required|min:2|max:25|unique:groups', // alpha_num
			
		
		);

	protected $messages = array(

			'regex' => 'valid format is group name colon(:) and 1 or 0'
		);
}
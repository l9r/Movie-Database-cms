<?php namespace Lib\Services\Validation;

class NewsValidator extends AbstractValidator
{
	public $rules = array(

			'title'	=> 'required|min:2|max:255|unique:news',
			'body'  => 'required|min:50',
			'image'	=> 'required|min:5|max:255',		
		);
}
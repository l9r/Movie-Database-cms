<?php namespace Lib\Services\Validation;

class PageValidator extends AbstractValidator
{
	public $rules = array(
		'title'       => 'required|min:2|max:255',
		'body'	      => 'required|min:10',
		'visibility'  => 'required|in:public,admin',
		'slug'	      => 'required|min:1|max:50',
	);
}
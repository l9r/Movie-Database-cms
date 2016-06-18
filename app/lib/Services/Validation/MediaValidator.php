<?php namespace Lib\Services\Validation;

class MediaValidator extends AbstractValidator
{
	public $rules = array(
		'file' => 'required|mimes:png,jpg,jpeg,gif|unique:images,local,NULL,id,type,upload',
	);
}
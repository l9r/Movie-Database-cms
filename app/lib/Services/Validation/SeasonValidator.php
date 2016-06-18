<?php namespace Lib\Services\Validation;

class SeasonValidator extends AbstractValidator
{
	public $rules = array(

		'title'    => 'required|min:1|max:255',
		'number'   => 'required|numeric|min:1|max:50',
		'overview' => 'min:1|max:1000',
		'release_date' => 'min:4|max:255',
		'poster'   => 'min:1, max:255'
		
		);

}
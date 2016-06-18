<?php namespace Lib\Services\Validation;

class ActorValidator extends AbstractValidator
{
	public $rules = array(

		'name'	=> 'required|min:2|max:255',
		'image'	=> 'min:5|max:255',
		'bio'	=> 'min:20|max:5000',
		'full_bio_link'	=> 'min:5|max:255',
		'awards' => 'min:5|max:255',
		'birth_date' => 'min:4|max:255',
		'birth_place' => 'min:3|max:255',
		'imdb_id' => 'max:255',
		'tmdb_id' => 'max:255'

		);

}
<?php namespace Lib\Services\Validation;

class TitleValidator extends AbstractValidator
{
	public $rules = array(

		'title'    => 'required|min:1|max:255',
		'type'	   => 'required|in:movie,series',
		
		);

	protected $image = array('image' => 'required|image|max:3072|mimes:jpeg,jpg,gif,png');

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
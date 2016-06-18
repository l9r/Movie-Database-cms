<?php namespace Lib\Services\Validation;

abstract class AbstractValidator
{
	protected $validator;

	public $rules = array();

	protected $data = array();

	protected $messages = array();

	protected $errors = array();

	/**
	* Set data to validate
	*
	* @param  array $data
	* @return \Impl\Service\Validation\AbstractLaravelValidation
	*/
	public function with(array $data)
	{
		$this->data = $data;

 		return $this;
 	}

 	/**
	* Validates provided data.
	*
	* @param  array $data
	* 
	* @return boolean
	*/
	public function passes()
	{
		$validator = \Validator::make($this->data, $this->rules, $this->messages);

		if ($validator->fails())
		{
			$this->errors = $validator->messages();

			return false;
		}

		return true;
 	}

 	/**
 	 * Returns errors.
 	 * 
 	 * @return array
 	 */
 	public function errors()
 	{
 		return $this->errors;
 	}

}
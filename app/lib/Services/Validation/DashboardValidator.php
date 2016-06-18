<?php namespace Lib\Services\Validation;

class DashboardValidator extends AbstractValidator
{
	public $rules = array(
		'username'    => 'required|alpha_num|min:3|max:20|unique:users',
		'email'	      => 'required|email|max:40|unique:users',
		'password'    => 'required|confirmed|min:5|max:30'
		);

	protected $messages = array(
		'exists' => 'Could not find a user with this email.'
		);

	protected $editInfo = array(
		'email'	      => 'required|email|max:40|unique:users',
		'gender'	  => 'in:male,female',
		'first_name'  => 'alpha_num|min:2|max:100',
		'last_name'   => 'alpha_num|min:2|max:100'
		);

	protected $avatar = array('avatar' => 'required|image|max:3072|mimes:jpeg,jpg,gif,png');

	protected $miniProfile = array('username' => 'required|alpha_num|min:3|max:20|exists:users');

	protected $imdbScrape = array(

		'minVotes'  => 'min:1|max:10000|numeric',
		'minRating' => 'min:1|max:10|numeric',
		'from'	    => 'required|min:1900|max:2020|numeric',
		'to'	    => 'required|min:1900|max:2020|numeric'
		);

	protected $options = array(

		'data_provider'     => 'alpha|in:imdb,tmdb,db',
		'tmdb_api_key'	    => 'min:1|max:255',
		'disqus_short_name' => 'min:1|max:255',
		'contact_us_email'	=> 'email|max:255',
		'url_delimiter'	    => 'min:1|max:1',
		'uri_case'		    => 'in:uppercase,lowercase',
		'require_act'       => 'in:1,0',
		);

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
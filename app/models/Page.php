<?php

class Page extends Entity {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	public $table = 'pages';

	/**
     * Fully qualified url for show page of resource.
     * 
     * @return string
     */
    public function getUrlAttribute()
    {
        return url($this->slug);   
    }

	/**
     * Append edit route url to json response, so we don't
     * have to construct it on client side.
     * 
     * @var array
     */
    protected $appends = array('url');

}
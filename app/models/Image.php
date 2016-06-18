<?php

class Image extends Entity {

	/**
	 * Model table.
	 * 
	 * @var string
	 */
	public $table = 'images';

    /**
     * Not fillable by mass asignment.
     * 
     * @var array
     */
    protected $guarded = array('id');

	/**
	 * Append path attribute to model.
	 * 
	 * @var array
	 */
    protected $appends = array('path');

	/**
     * Return either web or locale image path.
     * 
     * @param  string $value 
     * @return string
     */
    public function getPathAttribute()
    {
    	if ($this->local)
    	{
    		return url($this->local);
    	}

    	return url($this->web);
    }
}
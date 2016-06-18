<?php

class Link extends Entity {

	/**
	 * Model table.
	 * 
	 * @var string
	 */
	public $table = 'links';

    /**
     * Not fillable by mass asignment.
     * 
     * @var array
     */
    protected $guarded = array('id');

    /**
     * One to many relationship with title model.
     * 
     * @return Relationship
     */
    public function title()
    {
        return $this->belongsTo('Title');
    }
}
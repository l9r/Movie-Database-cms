<?php

class Category extends Entity
{
	public $table = 'categories';

	protected $guarded = array('id');

	public function title()
    {
        return $this->morphedByMany('Title', 'categorizable')->withPivot('created_at');
    }

    public function actor()
    {
        return $this->morphedByMany('Actor', 'categorizable')->withPivot('created_at');
    }

    public function getResourceTypeAttribute()
    {
    	if ($this->query == 'popularActors')
    	{
    		return 'actor';
    	}
    	else
    	{
    		return 'title';
    	}
    }
}
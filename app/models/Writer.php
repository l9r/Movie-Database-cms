<?php

class Writer extends Eloquent
{
	public function title()
    {
        return $this->belongsToMany('Title', 'writers_titles');
    }
}
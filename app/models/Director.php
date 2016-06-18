<?php

class Director extends Eloquent
{
	public function title()
    {
        return $this->belongsToMany('Title', 'directors_titles');
    }
}
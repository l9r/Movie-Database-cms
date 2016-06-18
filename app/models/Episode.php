<?php

class Episode extends Eloquent
{
	public function title()
    {
       return $this->belongsTo('Title');
    }

    public function season()
    {
       return $this->belongsTo('Season');
    }

     /**
     * Returns default image if title doesnt have poster.
     * 
     * @param  string $value 
     * @return string
     */
    public function getPosterAttribute($value)
    {
        if ($value && ! str_contains($value, 'http'))
        {
            return url($value);
        }

        return $value;
    }
}
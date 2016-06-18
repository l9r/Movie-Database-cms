<?php

class Actor extends Entity
{
    /**
     * Column to order items on if none is passed.
     * 
     * @var string
     */
    protected $defaultOrderColumn = 'views';

    public $table = 'actors';

    public function title()
    {
        return $this->belongsToMany('Title', 'actors_titles')
                    ->withPivot('known_for', 'char_name', 'id')
                    ->orderBy('release_date', 'desc');
    }

    public function category()
    {
        return $this->morphToMany('Category', 'categories');
    }

     /**
     * Returns default image if actor doesnt have an image.
     * 
     * @param  string $value 
     * @return string
     */
    public function getImageAttribute($value)
    {
        if ( ! $value)
        {
            return url('assets/images/imdbnoimage.jpg');
        }

        if ( ! str_contains($value, 'http'))
        {
            return url($value);
        }

        return $value;
    }

    /**
    * Fetches movies actor is known for.
    * 
    * @param  Illuminate\Database\Eloquent\Builder $query 
    * @return collection
    */
    public function scopeKnown($query)
    {
      return $query->where('featured', '=', 1)->limit(8)->orderBy('release_date', 'desc')->remember(10)->get();
    }

    /**
    * Returns actor model by actors name.
    * 
    * @param  Illuminate\Database\Eloquent\Builder $query 
    * @return collection
    */
    public function scopeByName($query, $name)
    {
      return $query->where('name', '=', $name)->firstOrFail();
    }
}


<?php

use Carbon\Carbon;

class Title extends Entity
{
    public $table = 'titles';

    protected $guarded = array('views');

    /**
     * Format genre so it can be used as a filter for grid.
     * 
     * @param  string $value 
     * @return string
     */
    public function getGenreAttribute($value)
    {
       $genre = str_replace(',', ' | ', $value);

       return trim($genre, ' | ');
    }

     /**
     * Limit titles to only ones current user added to given list.
     * 
     * @param  Illuminate\Database\Eloquent\Builder $query 
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInUsersList($query, $name = 'favorite', $id = null)
    {
        if ($id)
        {
            return $query->whereHas('user', function($q) use($id, $name)
            {
                $q->where('user_id', $id)->where($name, 1);
            });
        }

        return $query;
    }

    /**
     * Format budget.
     * 
     * @param  string $value 
     * @return string
     */
    public function getBudgetAttribute($value)
    {
        if ($value != '$0')
        {
            return $value;
        }
    }

    /**
     * Format revenue.
     * 
     * @param  string $value 
     * @return string
     */
    public function getRevenueAttribute($value)
    {
        if ($value != '$0')
        {
            return $value;
        }
    }

    /**
     * Return default background if none exists.
     * 
     * @param  string $value 
     * @return string
     */
    public function getBackgroundAttribute($value)
    {
        if ( ! $value)
        {
            return url('assets/images/cinema.jpg');
        }

        if ( ! str_contains($value, 'http'))
        {
            return url($value);
        }

        return $value;
    }

     /**
     * Returns default image if title doesnt have poster.
     * 
     * @param  string $value 
     * @return string
     */
    public function getPosterAttribute($value)
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
     * Formats release date before returning.
     * 
     * @param  string $value 
     * @return string
     */
    public function getReleaseDateAttribute($value)
    {
        //format release date if not already formatted
        if ( ! preg_match('/[a-z]|[A-Z]|-/', $value) && strlen($value) > 4)
        {
            return Carbon::createFromFormat('Y-m-d', $value)->toFormattedDateString();
        }

        return $value;
    }

    /**
     * Restrict query by genres.
     * 
     * @param  Illuminate\Database\Eloquent\Builder $query 
     * @return collection
     */
    public function scopeHasGenres($query, $genres = array('action'))
    {
        foreach ($genres as $genre)
        {
           $query->where('genre', 'LIKE', "%$genre%");
        }

        return $query;
    }


    public function actor()
    {
        return $this->belongsToMany('Actor', 'actors_titles')->withPivot('known_for', 'char_name', 'id');

    }

    public function user()
    {
        return $this->belongsToMany('User', 'users_titles')->withPivot('favorite', 'watchlist');
    }

    public function image()
    {
        return $this->hasMany('Image')->orderBy('created_at', 'asc');
    }

    public function director()
    {
       return $this->belongsToMany('Director', 'directors_titles');
    }

    public function category()
    {
        return $this->belongsToMany('Category', 'categories_titles');
    }

    public function writer()
    {
       return $this->belongsToMany('Writer', 'writers_titles');
    }

    public function review()
    {
       return $this->hasMany('Review');
    }

    public function season()
    {
        return $this->hasMany('Season')->orderBy('number', 'asc');
    }

    /**
     * Fetches title with relations by id.
     * 
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  Int $id
     * @return collection
     */
    public function scopeById($query, $id)
    {
        return $query->with('Actor', 'Image', 'Director', 'Writer', 'Review', 'Season.Episode')->findOrFail($id);
    }

     /**
     * Fetches all titles matching $id.
     * 
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  Int $id
     * @return collection
     */
    public function scopeByTempId($query, $id, $order = null)
    {
        if ($order)
        {
            return $query->where('temp_id', '=', $id)->orderBy($order, 'desc')->get();
        }

        return $query->where('temp_id', '=', $id)->get();      
    }
  
    /**
     * Fetches all information about series.
     * 
     * @param  Illuminate\Database\Eloquent\Builder $query 
     * @return collection
     */
    public function scopeSeries($query, $id)
    {
        return $query->where('id', '=', $id)->with('Season.Episode')->first();
    }

    /**
     * Updates titles information from tmdb or imdb.
     * 
     * @return void
     */
    public function updateFromExternal()
    {
        $title = App::make('Lib\Titles\TitleRepository');
        $title->getCompleteTitle($this);

        return true;
    }
}


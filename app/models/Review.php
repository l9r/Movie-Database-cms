<?php

class Review extends Entity
{
    protected $fillable = array('author', 'body', 'score');

    public $table = 'reviews';

    /**
     * Many to one association.
     * 
     * @return void
     */
    public function title()
    {
       return $this->belongsTo('Title');
    }

    /**
     * Returns recent reviews.
     * 
     * @param  Illuminate\Database\Eloquent\Builder $query 
     * @return collection
     */
    public function scopeRecent($query)
    {
      return $query->orderBy('created_at', 'desc')->limit(6)->get();
    }

    /**
     * Return default type if none exists.
     * 
     * @param  string $value 
     * @return string
     */
    public function getTypeAttribute($value)
    {
        if ( ! $value)
        {
            return 'critic';
        }

        return $value;
    }

    /**
     * Return default author if none exists.
     * 
     * @param  string $value 
     * @return string
     */
    public function getAuthorAttribute($value)
    {
        if ( ! $value)
        {
            return 'Unknown';
        }

        return $value;
    }

    /**
     * Format the date.
     * 
     * @param  string $value 
     * @return string
     */
    public function getCreatedAtAttribute($value)
    {
        try {
            return Carbon\Carbon::parse($value)->toFormattedDateString();
        } catch (Exception $e) {
            return $value;
        }
    }
 
}


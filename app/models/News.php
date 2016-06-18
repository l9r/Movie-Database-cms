<?php

class News extends Entity
{
    public $table = 'news';

    /**
     * Returns all news items paginated.
     * 
     * @param  Illuminate\Database\Eloquent\Builder $query 
     * @return collection
     */
    public function scopeNewsIndex($query)
    {
        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    /**
     * Fetches 1 latest news item.
     * 
     * @param  $query
     * @return News
     */
    public function scopeLastUpdated($query)
    {
        return $query->orderBy('created_at', 'DESC')->limit(1)->get();
    }

    /**
     * Updates news from external sources.
     * 
     * @return Collection
     */
    public function updateNews()
    {
        $s = App::make('Lib\Services\Scraping\Scraper');
        $s->updateNews();

        $news = $this->limit(11)->orderBy('created_at', 'desc')->get();

        return $news;
    }

      /**
     * Format created at date.
     *
     * @param string value
     * @return string
     */
    public function getCreatedAtAttribute($value)
    {
        return Carbon\Carbon::parse($value)->toFormattedDateString();
    }
    
}
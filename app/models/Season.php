<?php

class Season extends Eloquent
{
	/**
     * Writer instance.
     * 
     * @var Lib\Services\Db\Writer
     */
    private $dbWriter;

    public function __construct()
    {
        $this->dbWriter = App::make('Lib\Services\Db\Writer');
    }

    public function episode()
    {
        return $this->hasMany('Episode');
    }

    public function title()
    {
       return $this->belongsTo('Title');
    }

    /**
     * Fetches all episodes for series.
     * 
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  int $id series id indb
     * @param  int $num season number
     * @return collection
     */
    public function scopeEpisodes($query, $id, $num)
    {
       return $query->where('title_id', '=', $id)->whereNumber($num)->get();
    }

    /**
     * Inserts single seasons eps and returns title with everything.
     * 
     * @param  array  $season
     * @param  int $id
     * @return Collection
     */
    public function saveEpisodes(array $season, $id)
    {
        $this->dbWriter->CompileBatchInsert('episodes', $season)->save();

        return Title::whereId($id)->with('Writer', 'Director', 'Actor', 'Image', 'Review', 'Season.Episode')->first();
    }
}
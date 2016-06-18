<?php

class TVNetwork extends Entity
{
    public $table = 'tv_networks';

    protected $guarded = array('id');

    public static function updateProductionCompanyInfo($id)
    {
        return [
            'name'    => 'required|min:2|max:50|unique:tv_networks,name,' . $id,
            'description' => 'required',
            'website' => 'required'
        ];
    }

    public function titles()
    {
        return $this->hasMany('Title', 'related_to_id')->where('type', 'series')->orderBy('imdb_rating', 'desc')->orderBy('tmdb_rating', 'desc');
    }

}


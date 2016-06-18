<?php

class ProductionCompany extends Entity
{
    public $table = 'production_companies';

    protected $guarded = array('id');

    public static function updateProductionCompanyInfo($id)
    {
        return [
            'name'    => 'required|min:2|max:50|unique:production_companies,name,' . $id,
            'description' => 'required',
            'website' => 'required'
        ];
    }

    public function titles()
    {
        return $this->hasMany('Title', 'related_to_id')->where('type', 'movie')->orderBy('imdb_rating', 'desc')->orderBy('tmdb_rating', 'desc');
    }

}


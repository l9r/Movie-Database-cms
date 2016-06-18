<?php namespace Plugins\Streaming\Plugin\Lib\Titles;

use App;

class TitleRepository extends \Lib\Titles\TitleRepository {

    public function __construct()
    {
        $title = App::make('Title');
        $dbWriter = App::make('Lib\Services\Db\Writer');
        $images = App::make('Lib\Services\Images\ImageSaver');
        $review = App::make('Lib\Reviews\ReviewRepository');
        $provider = App::make('Lib\Repositories\Data\DataRepositoryInterface');

        parent::__construct($title, $dbWriter, $images, $review, $provider);
    }

    /**
     * Return most popular movies/series.
     *
     * @param string/int $limit
     * @return Collection
     */
    public function mostPopular($limit = 8)
    {   
        return $this->model->with('Link')
                    ->whereNotNull('poster')
                    ->whereNotNull('release_date')
                    ->orderBy('views', 'desc')
                    ->remember(2000, 'titles.mostPopular')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Builds paginate query with given parameters.
     * 
     * @param  array   $params
     * @param  integer $page
     * @param  integer $perPage
     * 
     * @return array
     */
    public function buildPaginateQuery(array $params, $page = 1, $perPage = 15)
    {
        $query = $this->model->table == 'links' ? $this->model->with('Title') : $this->model;

        $query = $this->appendParams($params, $query);

        $count = $query->cacheTags('count')->remember(2000)->count();

        $totalPages = $count / $perPage;
    
        $query = $query->skip($perPage * ($page - 1))->take($perPage);

        $query = $query->order(isset($params['order']) && $params['order'] ? $params['order'] : null);

        $query = $query->cacheTags(array($this->model->table, 'pagination'))->remember(2000);

        return array('query' => $query, 'totalPages' => $totalPages, 'totalItems' => $count);
    }
    
    /**
     * Restrict paginate query by given params.
     *
     * @param  array $params
     * @param  Builder $query
     * @return Builder
     */
    protected function appendParams(array $params, $query)
    {
        //append all the params to query from base paginate method
        $query = parent::appendParams($params, $query)->with('Link');

        if (isset($params['availToStream']) && $params['availToStream'] && $params['availToStream'] !== 'false')
        {
            $query->has('Link');
        }

        return $query;
    }
}
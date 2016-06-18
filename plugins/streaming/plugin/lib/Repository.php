<?php namespace Plugins\Streaming\Plugin\Lib;

use Helpers;

class Repository extends \Lib\Repository {

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
}
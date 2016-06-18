<?php namespace Lib;

use Helpers, App;

class Repository {
    
    /**
     * Count the number of records in model table.
     * 
     * @param  mixed $model
     * @return integer
     */
    public function count($name = null)
    {
        //if we get passed a model name count the records on the
        //table of that model, if not count the records on the child
        //class model
        if ($name)
        {
            $model = $this->getModel($name);

            return $model->remember(2000)->count();
        }

        return $this->model->remember(2000)->count();
    }

    /**
     * Instantiate new model instance from model name.
     * 
     * @param  string $name
     * @return Model
     */
    private function getModel($name)
    {
        $name = ucfirst($name);

        //make sure we don't trim last s from news item
        if ($name !== 'News')
        {
            return App::make(rtrim($name, 's'));
        }

        return App::make($name);
    }

    /**
     * Paginate titles.
     * 
     * @return array
     */
    public function paginate($params)
    {

        $data = array();

        $data['page'] = isset($params['page']) ? $params['page'] : 1;
        $data['perPage'] = isset($params['perPage']) ? $params['perPage'] : 15;

        $results = $this->buildPaginateQuery($params, $data['page'], $data['perPage']);

        $data['items'] = $results['query']->get()->toArray();

        $data['totalPages']  = $results['totalPages'];
        $data['totalItems']  = $results['totalItems'];
        
        return $data;
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
        $query = $this->model;
        if($this->model['table'] == 'users')
        {
            $query = $this->model->with('group');
        }

        //$query = $this->model;

        $query = $this->appendParams($params, $query);

        $count = $query->cacheTags('count')->remember(2000)->count();

        $totalPages = $count / $perPage;
    
        $query = $query->skip($perPage * ($page - 1))->take($perPage);

        $query = $query->order(isset($params['order']) && $params['order'] ? $params['order'] : null);

        $query = $query->cacheTags(array($this->model->table, 'pagination'))->remember(2000);
         
        return array('query' => $query, 'totalPages' => $totalPages, 'totalItems' => $count);
    }

    /**
     * Restrict query by given params.
     *
     * @param  array $params
     * @param  Builder $query
     * @return Builder
     */
    protected function appendParams(array $params, $query)
    {

        if (isset($params['query']))
        {
            $query = $query->whereTitleLike($params['query']);
        }

        if (isset($params['type']) && $params['type'])
        {
            $query = $query->where('type', $params['type']);
        }

        if (isset($params['related_to']) && $params['related_to'])
        {
            $query = $query->where('related_to_id', $params['related_to']);
        }

        return $query;
    }
	
}
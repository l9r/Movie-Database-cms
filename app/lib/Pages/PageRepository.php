<?php namespace Lib\Page;

use Carbon\Carbon;
use Lib\Repository;
use Page, Str, Event;

class PageRepository extends Repository {

	/**
	 * Page Model instance.
	 * 
	 * @var Page
	 */
	protected $model;

	/**
	 * Create new Page repository instance.
	 * 
	 * @param Page $model
	 */
	public function __construct(Page $model)
	{
		$this->model = $model;
	}

	/**
	 * Find a page by given id.
	 * 
	 * @param  int/string $id
	 * @return Page
	 */
	public function find($id)
	{
		return $this->model->findOrFail($id);
	}

	/**
	 * Get an array of all pages slugs.
	 * 
	 * @return array
	 */
	public function getPagesList()
	{
		return $this->model->remember(60, 'pages.all')->lists('slug');
	}

	/**
	 * Find a page by give slug.
	 * 
	 * @param  string $slug
	 * @return Page
	 */
	public function findBySlug($slug)
	{
		return $this->model->where('slug', $slug)->firstOrFail();
	}

	/**
	 * Save page to db or update if already exists.
	 * 
	 * @param  array  $value
	 * @param  mixed $table
	 * 
	 * @return boolean
	 */
	public function saveOrUpdate(array $values, $table = null)
	{
		$striped = array();

		//strip falsy values so we don't try to insert
		//to non existing columns
		foreach ($values as $key => $value)
		{
			if ($value)
			{
				$striped[$key] = $value;
			}

			//slugify the user supplied slug
			if ($key == 'slug')
			{
				$striped[$key] = Str::slug($value);
			}
		}

		return $this->model->saveOrUpdate(array($striped));
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
        $query = parent::appendParams($params, $query);

        $visibility = (isset($params['visibility']) && $params['visibility']) ? $params['visibility'] : 'public';
       
        $query = $query->where('visibility', $visibility);

        return $query;
    }


	/**
	 * Delete page with given id.
	 * 
	 * @param  int/string $id
	 * @return void
	 */
	public function destroy($id)
	{
		$resp = $this->model->destroy($id);

		Event::fire('Page.Deleted', Carbon::now());

		return $resp;
	}
}
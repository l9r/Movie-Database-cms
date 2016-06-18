<?php namespace Plugins\Streaming\Plugin\Lib\Categories;

use DB, App;
use Lib\Categories\CategoriesRepository;

class StreamingCategoriesRepository extends CategoriesRepository
{

	public function __construct()
	{
        $category = App::make('Category');

        parent::__construct($category);
	}

    /**
     * Return all categories.
     * 
     * @return Collection
     */
    public function all()
    {
        return $this->model->with(array('Title.Link', 'Actor'))->orderBy('weight', 'desc')->get();
    }
}
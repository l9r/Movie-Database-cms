<?php namespace Lib\Services\Rendering;

use App;
use Category;
use Illuminate\View\Factory as Environment;
use Illuminate\Cache\CacheManager;
use Lib\Categories\CategoriesRepository as CatRepo;

class HomepageRenderer {

	/**
	 * Laravel view instance.
	 * 
	 * @var Illuminate\View\Environment
	 */
	private $view;

	/**
	 * Category repository instance.
	 * 
	 * @var Lib\Categories\CategoriesRepository
	 */
	private $catRepo;

	/**
	 * Laravel Cache manager instance.
	 * 
	 * @var Illuminate\Cache\CacheManager
	 */
	private $cache;

	/**
	 * Create new HomepageRenderer instance.
	 * 
	 * @param Environment  $view   
	 * @param CatRepo      $catRepo
	 * @param CacheManager $cache  
	 */
	public function __construct(Environment $view, CatRepo $catRepo, CacheManager $cache)
	{
		$this->view = $view;
		$this->catRepo = $catRepo;
		$this->cache = $cache;
	}

	/**
	 * Render the homepage view for displaying.
	 *
	 * @param  string $name
	 * @return string
	 */
	public function render($name, $subname = 'Home.Content')
	{
		if ($this->cache->has('home.content'))
		{
		    $content = $this->cache->get('home.content');
		}
		else
		{	
			$slides = App::make('Lib\Slides\SlideRepository')->get();
			$news   = App::make('Lib\News\NewsRepository')->latest(8);

			$content = $this->view->make($subname)
							   ->with('slides', $slides)
							   ->with('news', $news)
							   ->with('categories', $this->getCategories())->render();
				
			$this->cache->put('home.content', $content, 2880);	
		}

		return $this->view->make($name)->with('content', $content);			
	}

	/**
	 * Get all the categories and update them in any need it.
	 * 
	 * @return Collection
	 */
	private function getCategories()
	{
		$categories = $this->catRepo->all();

		foreach ($categories as $k => $category)
		{
			if ($category->auto_update)
			{		
				try {
					$categories[$k] = $this->updateCategory($category);
				} catch (\Exception $e) {}
				
			}
		}

		return $categories;
	}

	/**
	 * Update the given category with new titles.
	 * 
	 * @param  Category $category
	 * @return Category
	 */
	private function updateCategory($category)
	{
		return $this->catRepo->attachByQuery($category->id, $category->query, $category->limit);
	}
}
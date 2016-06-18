<?php namespace Lib\Services;

use Carbon\Carbon;
use DB, App, Helpers;

class SitemapMaker {

	/**
	 * How much records to proccess per db query.
	 * 
	 * @var integer
	 */
	private $queryLimit = 50000;

	/**
	 * Resources to generate sitemap for and columns needed.
	 * 
	 * @var array
	 */
	private $resources = array(
		'titles' => array('title', 'id', 'type', 'updated_at'),
		'actors' => array('name', 'id', 'updated_at'),
		'news'   => array('title', 'id', 'updated_at'),
	);

	/**
	 * Create new SitemapMaker instance.
	 */
	public function __construct()
	{
		DB::disableQueryLog();
		ini_set('memory_limit','160M');
		ini_set('max_execution_time', 3600);
	}

	/**
	 * Generate a sitemap of all urls of the site.
	 * 
	 * @return void
	 */
	public function make()
	{
		$index = array();

		foreach ($this->resources as $name => $columns)
		{
			$index[$name] = $this->makeDynamicMaps($name, $columns);
		}

		$this->makeStaticMap();
		$this->makeIndex($index);
	}

	/**
	 * Create a sitemap for static pages.
	 * 
	 * @return void
	 */
	private function makeStaticMap()
	{
		$sitemap = App::make("sitemap");
		$sitemap->model->items = null;

		$sitemap->add(url(trans('main.privacyUrl')), Carbon::now(), '0.1', 'monthly');
    	$sitemap->add(url(trans('main.tosUrl')), Carbon::now(), '0.1', 'monthly');
    	$sitemap->add(url(trans('main.contactUrl')), Carbon::now(), '0.1', 'monthly');
    	$sitemap->add(url(trans('main.movies')), Carbon::now(), 1, 'weekly');
    	$sitemap->add(url(trans('main.series')), Carbon::now(), 1, 'weekly');
    	$sitemap->add(url(trans('main.people')), Carbon::now(), 1, 'weekly');
    	$sitemap->add(url(trans('main.news')), Carbon::now(), 1, 'weekly');
    	$sitemap->add(url('feed/news'), Carbon::now(), '0.8', 'weekly');
    	$sitemap->add(url('feed/new-and-upcoming'), Carbon::now(), '0.8', 'weekly');

    	$sitemap->store('xml', "static-pages-sitemap");
	}

	/**
	 * Create a sitemap index from all individual sitemaps.
	 * 
	 * @param  array  $index
	 * @return void
	 */
	private function makeIndex(array $index)
	{
		$sitemap = App::make("sitemap");

	    foreach ($index as $resource => $number)
	    {
	    	for ($i=1; $i <= $number; $i++)
		    { 
		        $sitemap->addSitemap(url("{$resource}-sitemap-$i.xml"));
		    }
	    }
	  
	    $sitemap->addSitemap(url("static-pages-sitemap.xml"));
	    $sitemap->store('sitemapindex', 'sitemap');
	}

	/**
	 * Create sitemaps for all dynamic resources.
	 * 
	 * @param  string $name   
	 * @param  array $columns
	 * @return integer         
	 */
	private function makeDynamicMaps($name, array $columns)
	{
		$fileName = $name;

		$toDo = DB::table($name)->count();
	    $done = 0;
	    $fileNum = 1;

	    while ($toDo > $done)
	    {    
	        $sitemap = App::make('sitemap');
	        $sitemap->model->items = null;
	       
	        $resources = DB::table($fileName)->orderBy('created_at', 'desc')->skip($done)->take($this->queryLimit)->get($columns);

	        foreach ($resources as $resource)
	        {
	            if ($name === 'titles') {
	            	$name = $resource->type;
	            }

	            if ($name === 'actors') {
	            	$name = 'people';
	            }
 
	            $sitemap->add(
	            	Helpers::url($name == 'people' ? $resource->name : $resource->title, $resource->id, $name),
	            	$resource->updated_at == '0000-00-00 00:00:00' ? Carbon::now()->toDateTimeString() : $resource->updated_at, 1, 'weekly'
	            );
	            
	        }

	        $sitemap->store('xml', "{$fileName}-sitemap-$fileNum");
	        
	        $done += $this->queryLimit;
	        $fileNum += 1;
	    }

	    return $fileNum-1;
	}
}
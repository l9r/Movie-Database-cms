<?php namespace Lib\Services\Search;

use File, Helpers, App, Actor, Title, Response;
use Illuminate\Cache\CacheManager;

class Autocomplete
{
	/**
	 * What results will be ordered results on.
	 * 
	 * @var string
	 */
	private $order = 'tmdb_popularity';

	public function __construct()
	{
		$this->order = Helpers::getOrdering();
	}

	/**
	 * Get data to auto populate slides.
	 * 
	 * @param  string $query
	 * @return Array      
	 */
	public function sliderPopulate($query)
	{
		$q = $this->prepareQuery($query);

		return Title::with('director', 'actor', 'image')->whereTitleLike($query)->orderBy($this->order, 'desc')->limit(8)->get()->toArray();
	}

	/**
	 * Provides autocplete when user types
	 * in words in searchbar.
	 * 
	 * @param  string $query
	 * @return json
	 */
	public function typeAhead($query)
	{	 	
		$q = $this->prepareQuery($query);

	    $titles = Title::limit(8)
	    			->where('title', 'LIKE', $q)
	    			->orderBy($this->order, 'desc')
	    			->cacheTags('autocomplete')
	    			->remember(1440)
	    			->get(array('id', 'title', 'poster', 'plot', 'genre', 'type', 'background'));

	    //prepare the array for display
	    foreach ($titles as $k => $v)
	    {
	    	//add title page url
	    	$v->link = Helpers::url($v->title, $v->id, $v->type);

	    	 //prepare poster
	    	 if ( ! $v->poster)
	    	 {
	    	 	$v->poster = asset('/assets/images/cinema.png');
	    	 }
	    	 else
	    	 {
	    	 	$v->poster = asset($v->poster);
	    	 }

	    	//remove any zeros for null plots
	    	if ( ! $v->plot)
	    	{
	    		$v->plot = null;
	    	}

	    	//format genre
	    	if ($v->genre)
	    	{
	    		$v->genre = trim( str_replace(',', ' | ', $v->genre), ' | ' );
	    	}
	    }

	    return Response::json($titles);		
	}

	/**
	 * Prepares users search term to be run
	 * against database records.
	 * 
	 * @param  string $query
	 * @return string
	 */
	private function prepareQuery($query)
	{
		$query = preg_replace("/[ -.:\/&]/i", '%', $query);
		
		return "%$query%";
	}

	/**
	 * Provides autocomplete for actor names
	 * when attaching new actor to title.
	 * 
	 * @param  string $query
	 * @return json
	 */
	public function castTypeAhead($query)
	{	 
		$q = $this->prepareQuery($query);

	    $actors = Actor::where('name', 'LIKE', $q)
	    				->select('id', 'name', 'image', 'bio')
	    				->limit(15)
	    				->get();

	  	//add placeholder image if actor doesnt have one in db
	    foreach ($actors as $k=> $v)
	    {
	    	if ( ! $v->image)
	    	{
	    		$v->image = asset('assets/images/noimage.jpg');
	    	}
	    	elseif ( ! str_contains('http', $v->image))
	    	{
	    		$v->image = asset($v->image);
	    	}
	    }

	    return Response::json($actors);
	}
}
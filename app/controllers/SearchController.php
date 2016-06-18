<?php

use Lib\Services\Cache\Cacher;
use Lib\Services\Validation\SearchValidator;
use Lib\Services\Search\Autocomplete as Auto;
use Lib\Services\Search\SearchProviderInterface as Search;

class SearchController extends BaseController
{
	/**
	 * Search provider instance.
	 * 
	 * @var Lib\Services\Search\SearchProviderInterface
	 */
	private $search;

	/**
	 * Autocomplete service instance.
	 * 
	 * @var Lib\Services\Autocomplete\Autocomplete
	 */
	private $autocomplete;

	/**
     * Options instace.
     * 
     * @var Lib\Services\Options\Options
     */
    private $options;

	/**
	 * Current search provider name.
	 * 
	 * @var string
	 */
	private $provider;

	public function __construct(Search $search, Auto $autocomplete)
	{
		$this->beforeFilter('csrf', array('on' => 'post'));

		$this->search = $search;
		$this->autocomplete = $autocomplete;

		//get search provider name for differianting between
		//different providers query caches
		$this->options = App::make('options');
		$this->provider = $this->options->getSearchProvider();

	}

	/**
	 * Use current dataprovider to perform seacrh
	 * by given query and return view with results.
	 * 
	 * @return View
	 */
	public function byQuery()
	{		
		$query = (string) Input::get('q');

		if ( ! $query || Str::length($query) <= 1)
			return View::make('Search.Results')->withTerm('');

		//don't encode the query if we will search our db as that will
		//cause problems
		if ( is_a($this->search, 'Lib\Services\Search\DbSearch') )
		{
			$encoded = $query;
		}
		else
		{
			$encoded = urlencode($query);
		}

		if ( ! Cache::tags('search')->has($this->provider.'search'.$encoded))
		{
			$results = $this->search->byQuery($encoded);
			Cache::tags('search')->put($this->provider.'search'.$encoded, $results, 8640);
		}
		else
		{
			$results = Cache::tags('search')->get($this->provider.'search'.$encoded);	
		}

		return View::make('Search.Results')->withData($results)->withTerm(e($query));
	}

	/**
	 * Provide autocomplete/suggest for titles.
	 * 
	 * @param  string $query
	 * @return json
	 */
	public function typeahead($query)
	{
		if ( ! Request::ajax() ) App::abort(404);	

		return $this->autocomplete->typeahead($query);
	}

	/**
	 * Data for autopopulating slider.
	 * 
	 * @param  string $query
	 * @return json
	 */
	public function populateSlider($query)
	{
		if ( ! Request::ajax() ) App::abort(404);

		return $this->autocomplete->sliderPopulate($query);
	}

	/**
	 * Provide autocomplete/suggest for actors.
	 * 
	 * @param  string $query
	 * @return json
	 */
	public function castTypeahead($query)
	{
		if ( ! Request::ajax() ) App::abort(404);

		return $this->autocomplete->castTypeahead($query);
	}

	
}

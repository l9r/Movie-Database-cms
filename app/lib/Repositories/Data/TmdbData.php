<?php namespace Lib\Repositories\Data;

use Carbon\Carbon;
use Lib\services\Scraping\Scraper;
use Lib\Services\Search\SearchProviderInterface;
use Helpers, Title, App, Actor, Exception, Event, Options;


class TmdbData implements DataRepositoryInterface, SearchProviderInterface
{
	/**
	 * Api base path.
	 * 
	 * @var string
	 */
	protected $base = 'http://api.themoviedb.org/3/';

	/**
	 * Youtube embed path.
	 * 
	 * @var string
	 */
	private $youtube = 'http://www.youtube.com/embed/';

	/**
	 * Api key.
	 * 
	 * @var string
	 */
	protected $key;

	/**
	 * Image base path.
	 * 
	 * @var string
	 */
	protected $imgBase = 'http://image.tmdb.org/t/p/';

	/**
	 * Titles id in db, if any.
	 * 
	 * @var string/int
	 */
	private $id;

	/**
	 * Titles type.
	 * 
	 * @var string
	 */
	private $type = 'movie';

	/**
	 * Api url append part.
	 * 
	 * @var string
	 */
	private $append = '&append_to_response=credits,videos,external_ids';

	/**
	 * Uncompiled api response.
	 * 
	 * @var array
	 */
	private $raw;

	/**
	 * Title trailer
	 * @var string
	 */
	private $trailer;

	/**
	 * Title cast.
	 * 
	 * @var array
	 */
	private $cast;

	/**
	 * Title genre(s)
	 * 
	 * @var string
	 */
	private $genre;

	/**
	 * Title images paths.
	 * 
	 * @var array
	 */
	private $images;

	/**
	 * Title directors.
	 * 
	 * @var array
	 */
	private $directors;

	/**
	 * Title writers.
	 * 
	 * @var array
	 */
	private $writers;

	/**
	 * Scraper instance.
	 * 
	 * @var Lib\services\Scraping\Scraper
	 */
	private $scraper;

	/**
	 * Title model
	 * 
	 * @var Title
	 */
	private $titleModel;

	/**
	 * Provider name
	 * 
	 * @var string
	 */
	public  $name = 'tmdb';

	/**
	 * What language to query api on.
	 * 
	 * @var string
	 */
	private $lang;

	public function __construct()
	{
		//resolve options singleton
		$options = App::make('options');

		$this->key = $options->getTmdbKey();

		//check if tmdb api key exists in database
		if ($this->key)
		{
			$this->scraper  = App::make('Lib\Services\Scraping\Curl');
			$this->key      = "api_key={$this->key}";
			$this->dbWriter = App::make('Lib\Services\Db\Writer');
			$this->lang     = $options->getTmdbLang();	
		}
	}

	/**
	 * Scrape and save titles from tmdb discover query.
	 * 
	 * @param  array $input
	 * @return integer
	 */
	public function discover(array $input)
	{
		ini_set('max_execution_time', 0);
		
		$url = $this->base . "discover/{$input['type']}?" . $this->key . "&language={$this->lang}";

		if ( ! $input['page'])  $input['page'] = 1;
		if ( ! $input['howMuch']) $input['howMuch'] = 120;

		$params = array_except( $input, array('_token', 'howMuch', 'type') );
		
		//loop trough all provided params and concat each one to url
		foreach ($params as $k => $v)
		{
			$url .= "&$k=$v";
		}

		//we've put * instead of dot earlier in form
		//to prevent it transforming into _
		$url = str_replace('*', '.', $url);

		return $this->loop($url, $input['howMuch'], $input['page']);
	}

	/**
	 * Loops trough discover query provided amount of times.
	 * 
	 * @param  string $url  
	 * @param  int $howMuch
	 * @param  int $start
	 * @return integer 
	 */
	private function loop($url, $howMuch, $start)
	{
		//prepare starting values for loop
		$pages = (int) round($howMuch);
		$num = 0;
		$currentPage = $start == 1 ? $start : $start+1;
		
		do {
			$url = preg_replace('/&page=[0-9]+/', "&page=$currentPage", $url);

			//increment values and url to next page
			$currentPage++;			
			$num += 20;

			$results = json_decode($this->call($url), true);
			
			if (isset($results['results']))
			{
				$compiled = $this->compileSearchResults($results['results']);
			
				$this->dbWriter->compileBatchInsert('titles', $compiled)->save();
			}
		}
		while ($num < $pages);
		
		return ($currentPage - $start - 1) * 20;	
	}

	/**
	 * Uses tmdb api to search for movies and tv shows.
	 * 
	 * @param  string $query
	 * @return array  merged array of matching tv shows and movies.
	 */
	public function byQuery($query)
	{
		$results = $this->searchByTitle($query);
		
		if ($results)
		{
			$compiled = $this->compileSearchResults($results);

			Event::fire('Search.ResultsCompiled', array($compiled, Carbon::now()));
		
			return $this->dbWriter->insertFromTmdbSearch($compiled);
		}	
	}

	/**
	 * Compiles insert ready array from tmdb search query results.
	 * 
	 * @param  array $results
	 * @return array
	 */
	private function compileSearchResults(array $results)
	{
		$filtered = array();
		$tempId = str_random(15);
		
		foreach ($results as $k => $v)
		{
			//its a movie
			if (isset($v['title']))
            {
                $movie = array();
      
                $movie['release_date'] = $v['release_date'];
                $movie['tmdb_rating']  = $v['vote_average'];
                $movie['title'] = $v['title'];
                $movie['original_title'] = $v['original_title'];
                $movie['year']  = substr($v['release_date'], 0, 4);
                $movie['tmdb_popularity'] = $v['popularity'];
                $movie['type'] = 'movie';

                if ($v['poster_path'])
                {
                	$movie['poster'] = $this->imgBase . 'w342' . $v['poster_path'];
                }
                else
                {
                	$movie['poster'] = null;
                }
                
                $movie['tmdb_id'] = $v['id'];
                $movie['temp_id'] = $tempId;
                
                array_push($filtered, $movie);
            }

            //its a series
            elseif (isset($v['name']))
            {              
                $series = array();

                $series['release_date'] = $v['first_air_date'];
                $series['tmdb_rating']  = $v['vote_average'];
                $series['title'] = $v['name'];
                $series['original_title'] = $v['original_name'];
                $series['year']  = substr($v['first_air_date'], 0, 4);
                $series['tmdb_popularity'] = $v['popularity'];
                $series['type']  = 'series';
                
               
                if ($v['poster_path'])
                {
                	$series['poster'] = $this->imgBase . 'w342' . $v['poster_path'];
                }
                else
                {
                	$series['poster'] = null;
                }

                $series['tmdb_id'] = $v['id'];
                $series['temp_id'] = $tempId;

                array_push($filtered, $series);
            }
		}

		return $filtered;
	}

	/**
	 * Get movies that are now playing in theaters.
	 * 
	 * @param  integer $return
	 * @return array
	 */
	public function getNowPlaying($return = 10)
	{
		$url = $this->base . 'movie/now_playing?' . $this->key . "&language={$this->lang}";

		$results = json_decode($this->call($url), true);

		if (count($results['results']) > $return)
		{
			$results = array_slice($results['results'], 0, $return);
		}
		else
		{
			$results = $results['results'];
		}

		return $this->formatNowPlaying($results);
	}

	/**
	 * Formats now playing movies into insert ready array.
	 * 
	 * @param  array $results
	 * @return mixed
	 */
	private function formatNowPlaying($results)
	{
		foreach ($results as $k => $v)
		{
			$compiled[] = array(
				'poster' 		  => $v['poster_path'] ? $this->imgBase . 'w342' . $v['poster_path'] : null,
				'title'	 		  => $v['title'],
				'tmdb_id' 		  => $v['id'],
				'release_date' 	  => $v['release_date'],
				'year'            => substr($v['release_date'], 0, 4),
				'tmdb_popularity' => $v['popularity'],
				'tmdb_rating'	  => $v['vote_average'],
				'updated_at'	  => Carbon::now(),
				'now_playing'	  => 1
				);
		}

		return (isset($compiled) ? $compiled : null);
	}

	/**
	 * gets the raw information on the title from current id and type.
	 * 
	 * @param  Title $model
	 * @return $this
	 */
	public function getFullTitle(Title $model)
	{
		//if model doesn't have tmdb_id, we'll bail
		if ( ! $model->tmdb_id) return $this;

		$this->titleModel = $model;
		$this->type = $model->type;

		$url = $this->compileSingleTitleUrl();

		$title = json_decode($this->call($url), true);

		$this->checkStatus($title, $model->id);
		
		$this->raw = $title;

		return $this;
	}

	/**
	 * Deletes title from db and throws 404 if tmdb
	 * returns invalid id status code.
	 * 
	 * @param  mixed $response
	 * @param  int $id
	 * @return void
	 */
	private function checkStatus($response, $id)
	{
		if (isset($response['status_code']) && $response['status_code'] === 6 ||
			isset($response['adult']) && $response['adult'] === true)
		{
			Title::find($id)->delete();
			App::abort(404);
		}
	}

	/**
	 * Compiles full url single titles tmdb api.
	 * 
	 * @return string
	 */
	private function compileSingleTitleUrl()
	{
		$type = 'tv';

		if ($this->titleModel->type == 'movie')
		{
			$type = 'movie';

			$this->append = '&append_to_response=casts,videos'; 		
		}

		return $this->base . "$type/" . "{$this->titleModel->tmdb_id}?" . $this->key . $this->append.",images&include_image_language={$this->lang},en,null" . "&language={$this->lang}";
	}

	/**
	 * Get current title id.
	 * 
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get current title tmdb id.
	 * 
	 * @return string
	 */
	public function getTmdbId()
	{
		if (isset($this->raw['id']))
		{
			return $this->raw['id'];
		}

		App::abort(404);
	}

	/**
	 * Get current title imdb id.
	 * 
	 * @return string
	 */
	public function getImdbId()
	{
        $id = isset($this->raw['imdb_id']) ? $this->raw['imdb_id'] : $this->raw['external_ids']['imdb_id'];
		
		if ($id) {
			return $id;
		} else {
			return null;
		}
	}

	/**
	 * Get current title title.
	 * 
	 * @return string
	 */
	public function getTitle()
	{
		
		if (isset($this->raw['title']))
		{
			return $this->raw['title'];
		}
		elseif (isset($this->raw['name']))
		{
			return $this->raw['name'];
		}
		
		return 'Unknown';
	}

	/**
	 * Get current title title.
	 * 
	 * @return string
	 */
	public function getOriginalTitle()
	{
		if (isset($this->raw['original_title']))
		{
			return $this->raw['original_title'];
		}
		elseif (isset($this->raw['original_name']))
		{
			return $this->raw['original_name'];
		}

		return 'Unknown';
	}

	/**
	 * Get current title poster.
	 * 
	 * @return mixed
	 */
	public function getPoster()
	{
		return ($this->raw['poster_path'] ? $this->imgBase . 'w342' . $this->raw['poster_path'] : null);
	}

	/**
	 * Get current title tagline.
	 * 
	 * @return string
	 */
	public function getTagline()
	{
		return (isset($this->raw['tagline']) ? $this->raw['tagline'] : null);
	}

	/**
	 * Get current title rating.
	 * 
	 * @return string
	 */
	public function getRating()
	{
		return (isset($this->raw['vote_average']) ? $this->raw['vote_average'] : null);
	}

	/**
	 * Get current title runtime.
	 * 
	 * @return string
	 */
	public function getRuntime()
	{
		return (array_key_exists('runtime', $this->raw) ? $this->raw['runtime'] : head($this->raw['episode_run_time']));
	}

	/**
	 * Get current title release date.
	 * 
	 * @return string
	 */
	public function getReleaseDate()
	{
		return (array_key_exists('release_date', $this->raw) ? $this->raw['release_date'] : $this->raw['first_air_date']);
	}

	/**
	 * Gets basic information about all series seasons.
	 * 
	 * @param  int $id 
	 * @return array
	 */
	public function getAllSeasons($id)
	{	
		if (isset($this->raw['seasons']))
		{
			//exlude special episodes '0' season
 			foreach ($this->raw['seasons'] as $k => $v)
 			{
 				if ((isset($v['season_number']) && $v['season_number'] === 0) || $v['season_number'] == null || $v['episode_count'] < 1)
 				{
 					unset($this->raw['seasons'][$k]);
 				}
 			}

			return $this->compileSeasons($this->raw['seasons'], $id);
		}
	}
	
	/**
	 * Fetches all seasons and episodes of series with full
	 * information by appending them to base series request.
	 * 
	 * @param  Title  $series
	 * @return array
	 */
	public function getFullAllSeasons(Title $series)
	{
		$data = array();

		$chunks = $series->season->chunk(20);

		//get seasons 20 at a time as that's the maximum on tmdb and then
		//merge them into one array
		foreach ($chunks as $chunk)
		{
			$url = $this->compileFullAllSeasonsUrl($series->tmdb_id, $chunk);
			$fetched = json_decode($this->call($url), true);

			//default to english for the season if first episode doesn't have a title in requested language
			if ( ! isset($fetched['season/1']['episodes'][0]['name']) || ! $fetched['season/1']['episodes'][0]['name']) {

				$oldLang = $this->lang;
				$this->lang = 'en';
				$url = $this->compileFullAllSeasonsUrl($series->tmdb_id, $chunk);
				$fetched = json_decode($this->call($url), true);
				$this->lang = $oldLang;
			}

			$data = array_merge($data, is_array($fetched) ? $fetched : array());
		}

		return $this->compileFullAllSeasons($data, $series->season);
	}

	/**
	 * Compiles url for all seasons with full information.
	 * 
	 * @param  int $id
	 * @param  collection $seasons
	 * @return string
	 */
	private function compileFullAllSeasonsUrl($id, $seasons)
	{	
		//make base url
		$url = $this->base . "tv/$id?" . $this->key . '&append_to_response=';

		//append each series season to request url
		foreach ($seasons as $k => $v)
		{
			$url .= "season/{$v->number},";
		}

		return rtrim($url, ',') . "&language={$this->lang}";
	}

	/**
	 * Compiles all seasons with full information including episodes.
	 * 
	 * @param  array  $data
	 * @param  collection $seasons
	 * @return array
	 */
	private function compileFullAllSeasons(array $data, $seasons)
	{
		$compiled = array();

		//loop trough provided seasons
		foreach ($seasons as $k => $v)
		{
			if (isset($data['season/'.$v->number]) && isset($v->id))
			{
				//match provided seasons to provided data
				$overview = $data['season/' . $v->number]['overview'];
				$title = $data['season/' . $v->number]['name'];
				$episodesArr = $data['season/' . $v->number]['episodes'];
				$episodes = $this->compileEpisodes($episodesArr, $v);

				$compiled[$k] = array('title' => $title, 'overview'  => $overview, 'updated_at' => Carbon::now(),
					'episodes' => $episodes, 'id' => $v->id, 'fully_scraped' => 1, 'title_tmdb_id' => $data['id']);		
			}
		
		}
		
		return $compiled;
	}

	/**
	 * Compiles seasons episodes into write/display ready array.
	 * 
	 * @param  array  $episodes 
	 * @param  collection $season
	 * @return array
	 */
	private function compileEpisodes(array $episodes, $season)
	{
		$compiled = array();

		foreach ($episodes as $k => $v)
		{
			$compiled[] = array(
				'release_date'   => $v['air_date'],
				'episode_number' => $v['episode_number'],
				'title'          => $v['name'],
				'plot'           => $v['overview'],
				'poster'         => $v['still_path'] ? $this->imgBase . 'w300' . $v['still_path'] : null,
				'title_id'       => $season->title_id,
				'season_id'	     => $season->id,
				'season_number'  => $season->number);		
		}
	
		return $compiled;
	}

	/**
	 * Formats the season array so its acceptable for database/view.
	 * 
	 * @param  array $seasons
	 * @return array
	 */
	private function compileSeasons($seasons, $id)
	{
		$compiled = array();

		foreach ($seasons as $k => $v)
		{
			$poster = ($v['poster_path'] ? $this->imgBase . 'w342' . $v['poster_path'] : null);

			$compiled[$k] = array('release_date' => $v['air_date'], 'poster' => $poster, 'number' => $v['season_number'], 'title_id' => $id);
		}

		return $compiled;
	}

	/**
	 * Get current title genre.
	 * 
	 * @return string
	 */
	public function getGenre()
	{
		if ( ! $this->genre)
		{
			$this->genre = $this->genre($this->raw['genres']);
		}

		return $this->genre;
	}

	/**
	 * Get current title genre.
	 * 
	 * @return string
	 */
	public function getLanguage()
	{
		$lang = '';

		if (isset($this->raw['spoken_languages']))
		{
			$langs = 'spoken_languages';
		}
		elseif(isset($this->raw['languages']))
		{
			$langs = 'languages';
		}
		else
		{
			return 'en';
		}

		foreach($this->raw[$langs] as $language)
		{
			if (is_array($language))
			{
				$lang .= "{$language['name']} | ";
			}
			else
			{
				$lang .= ucfirst($language) . ' | ';
			}		
		}

		return $lang ? trim($lang, ' | ') : 'en';
	}

	/**
	 * Get current title country.
	 * 
	 * @return string.
	 */
	public function getCountry()
	{
		$country = '';

		if (isset($this->raw['production_countries']))
		{
			foreach ($this->raw['production_countries'] as $k => $v)
			{
				$country .= head($v) . ', ';
			}
		}
		elseif (isset($this->raw['origin_country']))
		{
			foreach ($this->raw['origin_country'] as $k => $v)
			{
				$country .= $v . ', ';
			}
		}

		return $country != '' ? rtrim($country, ', ') : null;
	}

	/**
	 * Get current title plot.
	 * 
	 * @return string
	 */
	public function getPlot()
	{
		if (isset($this->raw['overview']) && $this->raw['overview'])
		{
			return $this->raw['overview'];
		}

		//default to english if no plot found in currrent url
		elseif ($this->lang != 'en')
		{
			$this->lang = 'en';

			$url = $this->compileSingleTitleUrl();

			$results = $this->call($url);
			$this->raw = json_decode($results, true);

			return $this->raw['overview'];
		}
	}

	/**
	 * Get current title budger.
	 * 
	 * @return string
	 */
	public function getBudget()
	{
		return (isset($this->raw['budget']) ? '$' . number_format($this->raw['budget']) : null);
	}

	/**
	 * Get current title revenue.
	 * 
	 * @return string
	 */
	public function getRevenue()
	{
		return (isset($this->raw['revenue']) ? '$' . number_format($this->raw['revenue']) : null);
	}

	/**
	 * Get current title cast.
	 * 
	 * @return string
	 */
	public function getCast()
	{
		if ( ! $this->cast)
		{
			//decide if its tv series or movie and send appropriate array
			$cast = (array_key_exists('casts', $this->raw) ? $this->raw['casts'] : $this->raw['credits']);

			$this->cast = $this->cast($cast);
		}

		return $this->cast;
	}

	/**
	 * Get current title directors.
	 * 
	 * @return string
	 */
	public function getDirectors()
	{
		if ( ! $this->directors)
		{
			$crew = (isset($this->raw['casts']['crew']) ? $this->raw['casts']['crew'] : null);
			if ( ! $crew) return;

			foreach ($crew as $k => $person)
			{
				//check what job every person has in array, if it's directors
				//push to directors array
				if ($person['job'] == 'Director')
				{
					$this->directors[$k] = $person['name'];
				}
			}
		}
		
		return $this->directors;
	}

	/**
	 * Get current title writers.
	 * 
	 * @return string
	 */
	public function getWriters()
	{
		if ( ! $this->writers)
		{
			$crew = (isset($this->raw['casts']['crew']) ? $this->raw['casts']['crew'] : $this->raw['credits']['crew']);

			foreach ($crew as $k => $person)
			{
				if ($person['department'] == 'Writing')
				{
					$this->writers[$k] = $person['name'];
				}
			}

			//push creators to writers if it's a tv show
			if (isset($this->raw['created_by']))
			{
				foreach ($this->raw['created_by'] as $creator)
				{
						$this->writers[] = $creator['name'];
				}
			}
		}
		
		return $this->writers;
	}

	/**
	 * Get current title images.
	 * 
	 * @return string
	 */
	public function getImages()
	{
		if ( ! $this->images)
		{
			$images = $this->raw['images']['backdrops'] ? $this->raw['images']['backdrops'] : array();
			$this->images = $this->images($images);
		}

		return $this->images;
	}

	/**
	 * Gets image background for title page jumbotron.
	 * 
	 * @return void|string
	 */
	public function getBackground()
	{
		if ( ! empty($this->raw['images']['backdrops']))
		{
			$key = array_rand($this->raw['images']['backdrops'], 1);

			$img = $this->raw['images']['backdrops'][$key];

			return $this->imgBase . 'w780' . $img['file_path'];
		}
	}

	/**
	 * Get current title trailer.
	 * 
	 * @return string
	 */
	public function getTrailer()
	{
		if ( ! $this->trailer)
		{
			$this->trailer = $this->trailers();
		}

		return $this->trailer;
	}

	/**
	 * Gets titles type, defaults to movie.
	 * 
	 * @return string.
	 */
	public function getType()
	{
		if ( ! $this->type) return 'movie';

		return $this->type;
	}

	/**
	 * Use tmdb api to search for movies/tv by title.
	 * 
	 * @param  string $query
	 * @return array
	 */
	private function searchByTitle($query)
	{
		$query = str_replace(' ', '+', $query);
		
		//construct urls using search query, will need to make 2 simultanious requests
		//since tmdb uses different urls for movies and tv
		$tv    = $this->base . 'search/tv?' . $this->key . '&query=' . $query . '&include_adult=false' . "&language={$this->lang}";
		$movie = $this->base . 'search/movie?' . $this->key . '&query=' . $query . '&include_adult=false' . "&language={$this->lang}";

		$results = $this->call(array($tv, $movie));
	
		//merge movie and tv result arrays
		$movies = json_decode($results[0], true);
		$tv     = json_decode($results[1], true);
		$response = array_merge($movies['results'], $tv['results']);
		
		return $response;
	}

	/**
	 * Get tmdb api config.
	 * 
	 * @return string
	 */
	private function getConfig()
	{
		$url = $this->base . 'configuration?' . $this->key;

		return $this->call($url);
	}

	/**
	 * Make a request to tmdb api with provided url(s).
	 * 
	 * @param  array/string $url
	 * @return string
	 */
	protected function call($url)
	{
		if ( ! $this->key)
		{
			throw new Exception('Please enter your api key in dashboard before using tmdb as data provider.');
		}
		
		if (is_array($url))
		{
			return $this->scraper->multiCurl($url);
		}
		
		return $this->scraper->curl($url);
	}

	/**
	 * Exctract single trailer from tmdb response.
	 * 
	 * @return string
	 */
	private function trailers()
	{
		if (isset($this->raw['videos']['results']) && is_array($this->raw['videos']['results']))
		{
			foreach ($this->raw['videos']['results'] as $video) {
				if (strtolower($video['type']) === 'trailer' && strtolower($video['site']) === 'youtube') {
					return $this->youtube . $video['key'];
				}
			}
		}

		return $this->scraper->getTrailer($this->getTitle(), $this->getReleaseDate());
	}

	/**
	 * Compile full images path.
	 * 
	 * @param  array $images
	 * @return mixed
	 */
	private function images(array $images)
	{
		foreach (array_slice($images, 0, 6) as $k => $v)
		{
			$compiled[] = array(
				'web' => $this->imgBase . 'w300' . $v['file_path']);
		}

		return (isset($compiled) ? $compiled : array());
	}

	/**
	 * Get current title genres.
	 * 
	 * @return string
	 */
	private function genre($genres)
	{
		if ( ! $genres) return;

		$compiled = '';

		foreach ($genres as $id => $array)
		{
			$compiled .= $array['name'] . ', ';
		}

		return (isset($compiled) ? trim($compiled, ', ') : array());
	}

	/**
	 * Compiles cast array.
	 * @param  array $cast
	 * @return mixed
	 */
	private function cast(array $cast)
	{
		foreach ($cast['cast'] as $key => $value)
		{
			$compiled[] = array(
				'tmdb_id' => $value['id'],
				'name' => $value['name'],
				'char' => $value['character'],
				'image' => ( ! $value['profile_path'] ? null : $this->imgBase . 'w342' . $value['profile_path'] ));
		}

		return (isset($compiled) ? $compiled : array());
	}

	/**
	 * Returns all the general info about title.
	 * 
	 * @return array
	 */
	public function getGenInfo()
	{
		$genInfo = array(		
			'title' => $this->getTitle(),
			'language' => $this->getLanguage(),
			'country'  => $this->getCountry(),
			'original_title' => $this->getOriginalTitle(),
			'plot' => $this->getPlot(),
			'updated_at'    => Carbon::now(),
			'tmdb_id' => $this->getTmdbId(),
			'poster' => $this->getPoster(),
			'tagline' => $this->getTagline(),
			'runtime' => $this->getRuntime(),
			'release_date' => $this->getReleaseDate(),
			'genre' => $this->getGenre(),
			'budget' => $this->getBudget(),	
			'imdb_id' => null,
			'revenue' => $this->getRevenue(),
			'type' => $this->getType(),
			'trailer' => $this->getTrailer(),
			'tmdb_rating' => $this->getRating()
		);

		return $genInfo;
	}

}
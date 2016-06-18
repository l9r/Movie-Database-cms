<?php namespace Lib\Repositories\Data;

use Carbon\Carbon;
use Symfony\Component\DomCrawler\Crawler;
use Lib\Repositories\Data\DataProviderInterface;
use App, Actor, Title, Review, Season, Helpers, Exception;


class ImdbData implements DataRepositoryInterface
{
	/**
	 * Crawler instance.
	 * 
	 * @var  \Symfony\Component\DomCrawler\Crawler
	 */
	private $crawler;

	/**
	 * Titles cast.
	 * 
	 * @var array
	 */
	private $cast;

	/**
	 * Titles type.
	 * 
	 * @var string
	 */
	private $type;

	/**
	 * All the divs with .txt-block class from imdb, we'll need
	 * to loop trough each to find some details about tile, like release date
	 * since there is no good way to reliably grab it with crawler.
	 * 
	 * @var array
	 */
	private $txtBlocks;

	/**
	 * Titles title.
	 * 
	 * @var string
	 */
	private $title;

	/**
	 * Titles plot.
	 * 
	 * @var string
	 */
	private $plot;

	/**
	 * Titles poster.
	 * 
	 * @var string
	 */
	private $poster;

	/**
	 * Titles release date.
	 * 
	 * @var string
	 */
	private $releaseDate;

	/**
	 * Titles genres.
	 * 
	 * @var string
	 */
	private $genre;

	/**
	 * Titles tagline.
	 * 
	 * @var string
	 */
	private $tagline;

	/**
	 * Titles awards.
	 * 
	 * @var string
	 */
	private $awards;

	/**
	 * Titles runtime.
	 * 
	 * @var string
	 */
	private $runtime;

	/**
	 * Titles imdb rating.
	 * 
	 * @var string
	 */
	private $imdbRating;

	/**
	 * Titles number of user votes in imdb.
	 * 
	 * @var string
	 */
	private $ImdbVotersAmount;

	/**
	 * Titles trailer.
	 * 
	 * @var string
	 */
	private $trailer;

	/**
	 * Titles directors.
	 * 
	 * @var array
	 */
	private $directors;

	/**
	 * Titles writers.
	 * 
	 * @var array
	 */
	private $writers;

	/**
	 * Titles model.
	 * 
	 * @var Title
	 */
	private $titleModel;

	/**
	 * Scraper instance.
	 * 
	 * @var \Lib\Services\Scraping\Curl
	 */
	private $scraper;

	/**
	 * Titles budget.
	 * 
	 * @var string
	 */
	private $budget;

	/**
	 * Titles revenue.
	 * 
	 * @var string
	 */
	private $revenue;

	/**
	 * Titles country.
	 * 
	 * @var string
	 */
	private $country;

	/**
	 * Titles language.
	 * 
	 * @var string
	 */
	private $language;

	/**
	 * Providers name.
	 * 
	 * @var string
	 */
	public $name = 'imdb';

	public function __construct($html = null)
	{
		$this->scraper = App::make('Lib\Services\Scraping\Curl');

		//if we get passed html trough constructor we'll innitiate a crawler
		if ($html)
		{
			//throw exception if calling code didn't pass in images or main title page html
			if ( ! isset($html[0]) || ! isset($html[1]))
			{
				throw new Exception ('ImdbParser needs imdb title page html and title images page html to work');
			}

			$this->crawler    = new Crawler($html[0]);
			$this->imgCrawler = new Crawler($html[1]);
		}	
	}

	/**
	 * Scrapes title main page and image page.
	 * 
	 * @param  Title  $model
	 * @return self
	 */
	public function getFullTitle(Title $model)
	{
		//if model doesn't have imdb_id, bail
		if ( ! $model->imdb_id) return $this;

		$this->titleModel = $model;
		$urls = $this->compileUrls();

		$html = $this->scraper->multiCurl($urls);

		$html['main'] = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html['main']);
		$html['imgs'] = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html['imgs']);

		$this->crawler = new Crawler($html['main']);
		$this->imgCrawler = new Crawler($html['imgs']);

		return $this;
	}

	/**
	 * Compiles titles page and titles
	 * images page urls.
	 * 
	 * @return array
	 */
	private function compileUrls()
	{
		$base  = 'http://www.imdb.com/';
		$end   = 'mediaindex?refine=still_frame';

		$urls['main'] = $base .'title/'. $this->titleModel->imdb_id . '/';
		$urls['imgs'] = $urls['main'] . $end;

		return $urls;
	}
	
	/**
	 * Gets all the general information about the title.
	 * 
	 * @return array
	 */
	public function getGenInfo()
	{
		return array('title'      => $this->getTitle(),      'release_date'   => $this->getReleaseDate(), 
					 'tagline'    => $this->getTagline(),    'genre'          => $this->getGenre(),
					 'plot'       => $this->getPlot(),       'imdb_rating'    => $this->getImdbRating(),
					 'awards'     => $this->getAwards(),     'runtime'        => $this->getRunTime(),
					 'type'       => $this->getType(),       'poster'         => $this->getPoster(),
				     'imdb_id'    => $this->getImdbId(),     'updated_at'     => Carbon::now(),
				     'trailer'	  => $this->getTrailer(),    'imdb_votes_num' => $this->getImdbVotersAmount(),
				     'budget'     => $this->getBudget(), 	 'revenue'        => $this->getRevenue(),
				     'language'   => $this->getLanguage(),   'country'        => $this->getCountry(),
				     'original_title' => $this->title);
					 
	}

	/**
	 * Gets titles imdbid.
	 * 
	 * @return string
	 */
	public function getImdbId()
	{
		$imdbid = $this->crawler->filter('link[rel="canonical"]')->extract(array('href'));
		$imdbid = str_replace('http://www.imdb.com/title/', '', head($imdbid));

		return trim($imdbid, '/');
	}

	/**
	 * Gets image background for title page jumbotron.
	 * 
	 * @return void|string
	 */
	public function getBackground()
	{
		$images = $this->getImages();

		if ( ! empty($images))
		{
			$key = array_rand($images, 1);
			
			return preg_replace('/._V1_.+?.jpg/', '._V1_SY1200_.jpg', $images[$key]);
		}
	}

	/**
	 * Uses youtube api to get a trailer for title.
	 * 
	 * @return string
	 */
	public function getTrailer()
	{
		if ($this->trailer)
		{
			return $this->trailer;
		}

		return $this->trailer = $this->scraper->getTrailer( $this->getTitle(), $this->getReleaseDate() );
	}

	/**
	 * Gets title.
	 * 
	 * @return string
	 */
	public function getTitle()
	{ 
		if ($this->title)
		{
			return $this->title;
		}

		//get the title in the native language of the curl request
		$native = trim(current($this->crawler->filter('title')->extract(array('_text'))));

		//extract only title from title string
		preg_match('/(.+?)\(.+?\) - IMDb/', $native, $title);

		//set native title, if for some reason we fail to get it we'll use a temp string to
		//prevent erroring out and so we'll be able to get proper title later 
		(isset($title[1]) ? $native = trim($title[1]) : $native = str_replace(' - IMDb', '', $native));

		//check if there's an original title;
		$original = head($this->crawler->filter('span.title-extra')->extract(array('_text')));

		//if so, then use the original
		if ($original)
		{
			//grab only the title from imdb orignal title string
			preg_match('/"(.+?)"/', $original, $matches);

			//if we fail to get the original (shouldn't happen) we'll fall back to native
			($matches[1] ? $this->title = $matches[1] : $this->title = $native);
		}
		//if not use the native one
		else
		{		
			$this->title = $native;
		}

		return $this->title;
	}

	/**
	 * Returns titles release date.
	 * 
	 * @return string 
	 */
	public function getReleaseDate()
	{  
		if ($this->releaseDate) return $this->releaseDate;

		if ( ! $this->txtBlocks || empty($this->txtBlocks))
		{
			$this->txtBlocks = $this->crawler->filter('.txt-block')->extract(array('_text'));
		}

		foreach ($this->txtBlocks as $string)
		{
			if (strpos($string, 'Release Date:'))
			{
				preg_match("/.*?Release Date: (.+?) \(.*?/", $string, $matches);

				try
				{
					return (isset($matches[1]) ? $this->releaseDate = date('Y-m-d', strtotime($matches[1])) : null);
				}
				catch (Exception $e)
				{
					return (isset($matches[1]) ? $this->releaseDate = $matches[1] : null);
				}
				
			} 
		}
	}

	/**
	 * Returns titles tagline.
	 * 
	 * @return string
	 */
	public function getTagline()
	{
		if ($this->tagline) return $this->tagline;

		if ( ! $this->txtBlocks || empty($this->txtBlocks))
		{
			$this->txtBlocks = $this->crawler->filter('.txt-block')->extract(array('_text'));
		}

		foreach ($this->txtBlocks as $string)
		{
			if (strpos($string, 'Taglines:'))
			{
				$tagline = str_replace('Taglines:', '', e($string));
				$tagline = str_replace('See more&nbsp;&raquo;', '', $tagline);
				
				return trim($tagline);
			} 
		}
	}

	/**
	 * Returns titles genres.
	 * 
	 * @return string
	 */
	public function getGenre()
	{
		$this->genre = implode(',', $this->crawler->filter('span[itemprop="genre"]')->extract(array('_text')));

		return trim($this->genre);
	}

	/**
	 * Returns titles plot.
	 * 
	 * @return array
	 */
	public function getPlot()
	{
		if ($this->plot)
		{
			return $this->plot;
		}

		$written = trim(head($this->crawler->filter('[itemprop="description"] > .nobr')->first()->extract(array('_text'))));
		$this->plot = trim(head($this->crawler->filter('[itemprop="description"] ')->first()->extract(array('_text'))));
		$this->plot = str_replace($written, '', $this->plot);

		return trim($this->plot);
	}

	/**
	 * Returns titles awards.
	 * 
	 * @return array
	 */
	public function getAwards()
	{	
		//if we already compiled awards return them
		if ($this->awards)
		{
			return $this->awards;
		}

		//if not compile now
		$this->awards = $this->crawler->filter('span[itemprop="awards"]')->extract(array('_text'));

		foreach ($this->awards as $k => $v)
		{
			//check if we have empty strings in awards array
			if (strlen($v) < 1)
			{
				//if so remove them
				unset($this->awards[$k]);
			}
		}

		//convert all awards into single string with and as separator
		$this->awards = implode('and', $this->awards);

		return $this->awards;
	}

	/**
	 * Returns titles budget.
	 * 
	 * @return string
	 */
	public function getBudget()
	{
		if ($this->budget) return $this->budget;

		if ( ! $this->txtBlocks || empty($this->txtBlocks))
		{
			$this->txtBlocks = $this->crawler->filter('.txt-block')->extract(array('_text'));
		}

		foreach ($this->txtBlocks as $string)
		{
			if (strpos($string, 'Budget:'))
			{
				$budget = str_replace('Budget:', '', e($string));
				$budget = str_replace('(estimated)', '', $budget);
				
				return trim($budget);
			} 
		}
	}

	/**
	 * Returns titles country.
	 * 
	 * @return string
	 */
	public function getCountry()
	{
		if ($this->country) return $this->country;

		if ( ! $this->txtBlocks || empty($this->txtBlocks))
		{
			$this->txtBlocks = $this->crawler->filter('.txt-block')->extract(array('_text'));
		}

		foreach ($this->txtBlocks as $string)
		{
			if (strpos($string, 'Country:'))
			{
				$cntr = '';

				$country = str_replace('Country:', '', e($string));
				$country = explode('|', $country);
				
				foreach ($country as $k => $v)
				{
					$cntr .= trim($v) . ', ';
				}

				return trim($cntr, ', ');
			} 
		}
	}

		/**
	 * Returns titles language.
	 * 
	 * @return string
	 */
	public function getLanguage()
	{
		if ($this->language) return $this->language;

		if ( ! $this->txtBlocks || empty($this->txtBlocks))
		{
			$this->txtBlocks = $this->crawler->filter('.txt-block')->extract(array('_text'));
		}

		foreach ($this->txtBlocks as $string)
		{
			if (strpos($string, 'Language:'))
			{
				$language = str_replace('Language:', '', e($string));
				
				return trim($language);
			} 
		}
	}

	/**
	 * Returns titles revenue.
	 * 
	 * @return string
	 */
	public function getRevenue()
	{
		if ($this->revenue) return $this->revenue;

		if ( ! $this->txtBlocks || empty($this->txtBlocks))
		{
			$this->txtBlocks = $this->crawler->filter('.txt-block')->extract(array('_text'));
		}

		foreach ($this->txtBlocks as $string)
		{
			if (strpos($string, 'Gross:'))
			{
				$revenue = str_replace('Gross:', '', e($string));
				$revenue = preg_replace('/\(.*?\)/', '', $revenue);
				
				return trim($revenue);
			} 
		}
	}

	/**
	 * Returns titles imdb rating.
	 * 
	 * @return float
	 */
	public function getImdbRating()
	{
		return (float) $this->imdbRating = head($this->crawler->filter('span[itemprop="ratingValue"]')->extract(array('_text')));
	}

	/**
	 * Returns titles  number of votes.
	 * 
	 * @return float
	 */
	public function getImdbVotersAmount()
	{
		if ($this->ImdbVotersAmount)
		{
			return $this->ImdbVotersAmount;
		}

		$amount = head($this->crawler->filter('span[itemprop="ratingCount"]')->extract(array('_text')));

    	$amount = str_replace(',', '', $amount);

    	return $this->ImdbVotersAmount = (int) $amount;
	}

	/**
	 * Returns titles runtime.
	 * 
	 * @return string
	 */
	public function getRuntime()
	{
		if ($this->runtime)
		{
			return $this->runtime;
		}

		$this->runtime = head($this->crawler->filter('time[itemprop="duration"]')->extract(array('_text')));
		$this->runtime = preg_replace('/[^0-9]/', '', $this->runtime);
		
		return $this->runtime;
	}

	/**
	 * Returns titles poster.
	 * 
	 * @return string
	 */
	public function getPoster()
	{	
		if ($this->poster)
		{
			return $this->poster;
		}

		$this->poster = head($this->crawler->filter('#img_primary img')->extract(array('src')));

		return $this->poster;	
	}

	/**
	 * Returns titles images.
	 * 
	 * @return array
	 */
	public function getImages()
	{	
		$images = $this->imgCrawler->filter('a[itemprop="thumbnailUrl"] > img[itemprop="image"]')->extract(array('src'));

		return $images;
	}


	/**
	 * Returns titles type.
	 * 
	 * @return string
	 */
	public function getType()
	{
		if ($this->type)
		{
			return $this->type;
		}

		$title = trim(current($this->crawler->filter('title')->extract(array('_text'))));
		
		if (str_contains($title, 'TV Movie'))
		{
			return $this->type = 'movie';
		}
		elseif (str_contains($title, 'TV'))
		{
			return $this->type = 'series';
		}

		return $this->type = 'movie';
	}

	/**
	 * Returns basic information about all series seasons.
	 * 
	 * @return array/void
	 */
	public function getAllSeasons($id)
	{
		//extract string with all the season numbers
		$seasons = $this->crawler->filter('.seasons-and-year-nav > div')->eq(2)->filter('a')->extract(array('_text'));

		return $this->compileAllSeasons($seasons);
	}

	/**
	 * Compiles db insert ready array of all series seasons.
	 * 
	 * @param  array $seasons
	 * @return void/array
	 */
	private function compileAllSeasons(array $seasons)
	{
		$compiled = array();

		if ( count($seasons))
		{
			foreach ($seasons as $number)
			{
				if (strlen((string) $number) < 4)
				{
					$compiled[] = array('number' => $number, 'title_id' => $this->titleModel->id, 'title_imdb_id' => $this->getImdbId());
				}
			}

			return array_reverse($compiled);
		}
	}

	/**
	 * Compiles all the information about episodes of single season.
	 * 
	 * @param  Title $title
	 * @return 
	 */
	public function getSingleSeason(Title $title, $num)
	{
		$url = $this->compileSingleSeasonUrl($title->imdb_id, $num);

		$html = $this->scraper->curl($url);
		$html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);

		$season = Helpers::extractSeason($title, $num);

		return $this->compileSingleSeason($html, $season);
	}

	/**
	 * Returns fully quelified single season imdb url.
	 * 
	 * @param  string $imdbid
	 * @param  int $number
	 * @return string
	 */
	private function compileSingleSeasonUrl($imdbid, $number)
	{
		return "http://www.imdb.com/title/$imdbid/episodes?season=$number";
	}

	/**
	 * Compiles information about all the episodes of season.
	 * 
	 * @param  string $html 
	 * @param  Season $season
	 * @return array/void
	 */
	private function compileSingleSeason($html, Season $season)
	{
		$crawler = new crawler($html);

		//get all episodes
		$episodes = $crawler->filter('.eplist .list_item');

		//filter trough each episode and compile array if info
		foreach ($episodes as $ep)
		{
			$crawler = new crawler($ep);

			$epnum = $crawler->filter('.hover-over-image > div')->extract('_text');
			$img   = head( $crawler->filter('img')->extract('src'));

			$compiled[] = array(

				'poster' => $this->saveImage($img, $this->extractEpisodeNumber($epnum), $season),
				'title'  => head( $crawler->filter('a[itemprop="name"]')->extract('_text')),
				'release_date'   => trim( head( $crawler->filter('.airdate')->extract('_text'))),
				'plot'   => head( $crawler->filter('div[itemprop="description"]')->extract('_text')),
				'title_id' => $season->title_id,
				'season_id' => $season->id,
				'season_number' => $season->number,
				'episode_number' => $this->extractEpisodeNumber($epnum),
				'updated_at' => Carbon::now()
			);		
		}

		return (isset($compiled) ? $compiled : null);
	}

	/**
	 * Saves episodes poster locally.
	 * 
	 * @param  string $url
	 * @param  string $epnum
	 * @param  Season $season
	 * @return string
	 */
	private function saveImage($url, $epnum, Season $season)
	{
		$images = App::make('Lib\Services\Images\ImageSaver');

		//if its placeholder image on imdb, bail and leave it null
		if ( str_contains($url, 'nopicture') ) return;

		$path = 'imdb/episodes/';
		$num  = $season->number . $epnum;
		$id   = $season->title_imdb_id;

		$images->saveSingle(Helpers::size($url, 2), $id, $path, $num);

		return $path . $id . $num . '.jpg';
	}

	/**
	 * Extracts episode number from string.
	 * 
	 * @param  array  $number
	 * @return string/void
	 */
	private function extractEpisodeNumber(array $number)
	{
		preg_match('/.*?Ep([0-9]+)/', head($number), $match);

		if (isset($match[1]))
		{
			return $match[1];
		}
	}

	/**
	 * Fetches now playing movies from imdb.
	 * 
	 * @return array
	 */
	public function getNowPlaying()
	{
		$url = 'http://www.imdb.com/movies-in-theaters/';

		$html = $this->scraper->curl($url);

		$crawler = new Crawler($html);

		//grab all the movie divs
		$titles = $crawler->filter('.list.detail')->eq(1)->filter('.list_item');

		return $this->compileNowPLaying($titles);
	}

	/**
	 * Compiles now playing title array for insert.
	 * 
	 * @param  Crawler $titles
	 * @return array
	 */
	private function compileNowPLaying(Crawler $titles)
	{
		foreach ($titles as $k => $v)
		{
			$cr = new Crawler($v);

			$title   = head( $cr->filter('h4[itemprop="name"]')->extract('_text') );
			$runtime = head( $cr->filter('time[itemprop="duration"]')->extract('_text') );
			$genre   = $cr->filter('span[itemprop="genre"]')->extract('_text');
			$id      = head( $cr->filter('h4[itemprop="name"] > a')->extract('href') );
			$titleSpl= explode('(', $title);

			$compiled[] = array(

				//get only title from string
				'title' => trim( $titleSpl[0] ),

				//get year from title string
				'year'    => trim( $titleSpl[1], ')' ),
				'runtime' => trim( $runtime, ' min'),
				'type'	  => 'movie',
				'genre'	  => implode(' | ', $genre),
				'imdb_id' => $this->extractId($id),
				'imdb_rating'  => head( $cr->filter('.rating-rating > .value')->extract('_text')),
				'plot'	  => trim(head( $cr->filter('div[itemprop="description"]')->extract('_text'))),			
				'poster'  => Helpers::size( head( $cr->filter('img.poster')->extract('src')), 2),
				'now_playing' => 1,
				'imdb_votes_num'  => trim(head( $cr->filter('meta[itemprop="ratingCount"]')->extract('content')))
				);
		}

		return isset($compiled) ? $compiled : array();
	}

	private function extractId($id)
	{
		preg_match('/(tt[0-9]+)/', $id, $matches);

		return isset($matches[1]) ? $matches[1] : $id;
	}

	/**
	 * Returns titles writers.
	 * 
	 * @return array
	 */
	public function getWriters()
	{
		//check if we already compiled an array of writers
		if ($this->writers)
		{
			return $this->writers;
		}

		//if not compile it now
		$this->writers = $this->crawler->filter('div[itemprop="creator"] > a > span')->extract(array('_text'));

		return $this->writers;
	}

	/**
	 * Get current titles id in database.
	 * 
	 * @return int
	 */
	public function getId()
	{
		return $this->titleModel->id;
	}

	/**
	 * Returns titles cast.
	 * 
	 * @return array
	 */
	public function getCast()
	{
		if ( ! $this->cast)
		{			
			$this->cast = $this->compileCast();
		}

		return $this->cast;
	}

	/**
	 * Compiles titles cast.
	 * 
	 * @return array
	 */
	private function compileCast()
	{
		//get all the actor/char rows from imdb
		$raw = $this->crawler->filter('table.cast_list > tr.odd, table.cast_list > tr.even');

		//foreach row extract image, id, actor name and actors character(s)
		foreach ($raw as $k => $v)
		{
			//skip parsing first row since its not actor
			$crawler = new crawler($v);

			//get actor name and image
			$actor = head($crawler->filter('.primary_photo > a > img')->extract(array('loadlate', 'title')));

			//get actor id
			$actorid = Helpers::extract(head($crawler->filter('.primary_photo > a')->extract('href')), 'nm');

			//get char
			$char = head($crawler->filter('.character')->extract('_text'));
			$char = $this->prettify($char);

			//push all data into cast array
			$cast[last($actor)] = array('name' => last($actor), 'image' => head($actor), 'char' => $char, 'imdb_id' => $actorid);		
		}

		return isset($cast) ? $cast : array();
	}

	/**
	 * Returns title directors.
	 * 
	 * @return array
	 */
	public function getDirectors()
	{
		if ( ! $this->directors)
		{
			$this->directors = $this->crawler->filter('div[itemprop="director"] > a > span')->extract(array('_text'));
		}

		return $this->directors;
	}

	/**
	 * Trims not needed spaces and characters from strings.
	 * 
	 * @param  string $string
	 * @return string
	 */
	private function prettify($string)
	{
		$string = explode('(', $string);
		$string = $string[0];

		//return None incase actor has no char specified
		if ( ! preg_match('/[a-z]|[A-Z]|[0-9]/', $string))
		{
			return 'None';
		}
		else
		{
			//remove non breaking spaces
			$string = str_replace('&nbsp;', '', e($string));

			//trim and decode string back to the original
			return trim( html_entity_decode($string, ENT_QUOTES, 'UTF-8') );
		}
	}	
}